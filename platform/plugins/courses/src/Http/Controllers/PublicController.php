<?php

namespace Botble\Courses\Http\Controllers;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Courses\DataTransferObjects\CourseSearchParams;
use Botble\Courses\Services\GetCourseService;
use Botble\Hotel\Enums\BookingStatusEnum;
use Botble\Hotel\Enums\CustomerCardCoverageType;
use Botble\Hotel\Facades\HotelHelper;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Botble\Slug\Facades\SlugHelper;
use Botble\Courses\Models\Course;
use Botble\Courses\Models\CourseSession;
use Botble\Courses\Models\CourseBooking;
use Botble\SeoHelper\SeoOpenGraph;
use Botble\Base\Facades\Html;
use Illuminate\Support\Str;
use Botble\Courses\Models\CourseCategory;
use Botble\Optimize\Facades\OptimizerHelper;
use Botble\Hotel\Models\Currency;
use Botble\Hotel\Models\Customer;
use Botble\Hotel\Services\CustomerCardPricingService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Botble\Hotel\Services\CustomerCardService;
use Botble\Courses\Http\Requests\InitBookingRequest;
use Botble\Courses\Http\Requests\CalculateBookingAmountRequest;
use Botble\Courses\Services\CourseBookingService;
use Botble\Courses\Services\CourseCheckoutStateService;
use Botble\Payment\Supports\PaymentHelper;
use Botble\Payment\Services\Gateways\BankTransferPaymentService;
use Botble\Payment\Services\Gateways\CodPaymentService;
use Botble\Base\Facades\BaseHelper;
use Botble\Courses\Http\Requests\CourseCheckoutRequest;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Models\Payment;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Throwable;
use Botble\Hotel\Supports\HotelSupport;

class PublicController extends Controller
{
    protected const MINIMUM_ONLINE_PAYMENT_AMOUNT = 0.9;

    public function __construct(
        protected GetCourseService $getCourseService
    ) {
    }

    public function getCourses(Request $request, BaseHttpResponse $response)
    {
        SeoHelper::setTitle(__('Courses'));

        Theme::breadcrumb()->add(__('Courses'), route('public.courses'));

        if ($request->ajax() && $request->wantsJson()) {

            $params = CourseSearchParams::fromRequest($request->input());
            $courses = $this->getCourseService->getCourses($params);

            $data = '';
            foreach ($courses as $course) {
                $data .= view(
                    Theme::getThemeNamespace('views.courses.includes.course-item'),
                    compact('course')
                )->render();
            }

            return $response->setData($data);
        }

        return Theme::scope('courses.courses')->render();
    }

    public function getCourse(string $key)
    {
        $slug = SlugHelper::getSlug($key, SlugHelper::getPrefix(Course::class));
        abort_unless($slug, 404);

        $course = Course::query()
            ->with(['instructor', 'category', 'sessions' => function ($q) {
                $q->where('start_date', '>=', now())->orderBy('start_date');
            }])
            ->findOrFail($slug->reference_id);

        SeoHelper::setTitle($course->name)->setDescription(Str::words($course->description, 120));

        $meta = new SeoOpenGraph();
        if ($course->thumbnail) {
            $meta->setImage(get_image_url($course->thumbnail));
        }
        $meta->setDescription($course->description);
        $meta->setUrl($course->url);
        $meta->setTitle($course->name);
        $meta->setType('article');

        SeoHelper::setSeoOpenGraph($meta);

        Theme::breadcrumb()->add(__('Courses'), route('public.courses'))
            ->add($course->name, $course->url);

        if (function_exists('admin_bar')) {
            admin_bar()->registerLink(__('Edit this course'), route('course.edit', $course->getKey()));
        }

        $relatedCourses = $this->getCourseService->getRelatedCourses(
            $course->getKey(),
            (int) theme_option('number_of_related_courses', 4),
            ['with' => ['instructor', 'category']]
        );

        Theme::asset()->add('ckeditor-content-styles', 'vendor/core/core/base/libraries/ckeditor/content-styles.css');
        $course->description = Html::tag('div', (string) $course->description, ['class' => 'ck-content'])->toHtml();

        return Theme::scope('courses.course', compact('course', 'relatedCourses'))->render();
    }

    public function getCourseCategory(string $key)
    {
        $slug = SlugHelper::getSlug($key, SlugHelper::getPrefix(CourseCategory::class));

        abort_unless($slug, 404);

        $category = $slug->reference;

        abort_unless($category->getKey(), 404);

        SeoHelper::setTitle($category->name)->setDescription(Str::words($category->description, 120));

        $meta = new SeoOpenGraph();
        $meta->setDescription($category->description);
        $meta->setUrl($category->url);
        $meta->setTitle($category->name);
        $meta->setType('article');

        SeoHelper::setSeoOpenGraph($meta);

        Theme::breadcrumb()
            ->add(__('Courses'), route('public.courses'))
            ->add($category->name, $category->url);

        do_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, COURSE_MODULE_SCREEN_NAME, $category);

        $courses = Course::query()
            ->whereHas('category', function ($query) use ($category) {
                return $query->where('id', $category->getKey());
            })
            ->wherePublished()
            ->paginate();

        return Theme::scope('courses.category', compact('courses', 'category'))->render();
    }

    public function postCourseBooking(InitBookingRequest $request, BaseHttpResponse $response)
    {
        $course = Course::query()->findOrFail($request->input('course_id'));
        $session = CourseSession::query()->findOrFail($request->input('session_id'));

        if (! $session->hasAvailableSeats()) {
            return $response
                ->setError()
                ->setNextUrl(route('public.courses'))
                ->setMessage(__('No seats available for this session.'));
        }

        $token = md5(Str::random(40));

        session([
            $token => $request->except(['_token']),
            'course_checkout_token' => $token,
            'checkout_token' => $token,
            'checkout_context' => \Botble\Hotel\Supports\HotelSupport::CONTEXT_COURSE,
        ]);

        return $response->setNextUrl(route('public.course.booking.form', $token));
    }

    public function getCourseBooking(
        string $token,
        BaseHttpResponse $response,
        CustomerCardService $customerCardService
    )
    {
        SeoHelper::setTitle(__('Course Booking'));
        OptimizerHelper::disable();

        $customer = new Customer();

        if (Auth::guard('customer')->check()) {
            $customer = Auth::guard('customer')->user();
        }

        $sessionData = [];
        if (session()->has($token)) {
            $sessionData = session($token);
        }

        abort_if(empty($sessionData), 404);

        Theme::breadcrumb()->add(__('Booking'), route('public.courses'));

        $course = Course::query()->findOrFail(Arr::get($sessionData, 'course_id'));
        $session = CourseSession::query()->findOrFail(Arr::get($sessionData, 'session_id'));

        $customerCardsAllowed = (bool) $course->accept_customer_card;

        $pricing = $course->resolvePricing($customer);
        $courseGrossPrice = (float) ($pricing['calculated_gross'] ?? $course->getPriceWithTax($course->getCourseTotalPrice()));
        $priceBreakdown = course_price_breakdown($course, $customer);

        $amountNetRaw = (float) ($pricing['calculated_net'] ?? 0);
        $amountNet = course_truncate_price($amountNetRaw);
        $amount = course_truncate_price((float) ($pricing['calculated_gross'] ?? 0));
        $basePrice = course_truncate_price((float) ($pricing['base_net'] ?? 0));
        $discountAmount = course_truncate_price((float) ($pricing['discount_gross'] ?? 0));

        $couponAmountNet = (float) Arr::get($sessionData, 'coupon_amount', 0);
        $couponAmountNet = min($couponAmountNet, $amountNetRaw);
        $couponCode = Arr::get($sessionData, 'coupon_code');

        $netSubtotalRaw = max($amountNetRaw - $couponAmountNet, 0);
        $netSubtotal = course_truncate_price($netSubtotalRaw);
        $taxAmountRaw = $course->getTaxAmount($netSubtotalRaw);
        $taxAmount = course_truncate_price($taxAmountRaw);
        $totalRaw = $netSubtotalRaw + $taxAmountRaw;
        $total = course_truncate_price($totalRaw);
        $couponAmount = course_truncate_price($course->getPriceWithTax($couponAmountNet));
        $checkoutData = HotelHelper::getCheckoutData(null, HotelSupport::CONTEXT_COURSE);

        $availableCards = collect();
        $selectedCard = null;
        $cardDiscount = 0.0;
        $cardUnitsUsed = max((int) data_get($checkoutData, 'customer_card_units_used', 1), 1);
        $cardCoverageTypeValue = data_get($checkoutData, 'customer_card_coverage_type');
        $cardCoverageType = $cardCoverageTypeValue
            ? CustomerCardCoverageType::tryFrom($cardCoverageTypeValue)
            : null;
        $cardUnitPrice = (float) data_get($checkoutData, 'customer_card_unit_price', 0);

        if ($customerCardsAllowed && $customer->id) {
            $availableCards = $customerCardService->getApplicableCardsForCourse($course->id, $customer->getKey());

            $cardId = (int) data_get($checkoutData, 'customer_card_id');

            if ($cardId) {
                $selectedCard = $customerCardService->getValidCard($cardId, $customer->getKey());

                if ($selectedCard) {
                    $pricingService = app(\Botble\Hotel\Services\CustomerCardPricingService::class);
                    $cardEffect = $pricingService->calculateCardEffect($selectedCard, $course, $totalRaw);
                    $cardDiscount = course_truncate_price(min(
                        (float) data_get($checkoutData, 'customer_card_discount', $cardEffect->discountGross),
                        $totalRaw
                    ));
                    $cardUnitsUsed = $cardEffect->unitsUsed;
                    $cardCoverageType = $cardEffect->coverageType;
                    $cardCoverageTypeValue = $cardCoverageType->value;
                    $cardUnitPrice = $cardEffect->unitValueGross;

                    HotelHelper::saveCheckoutData([
                        'customer_card_id' => $selectedCard->getKey(),
                        'customer_card_discount' => $cardDiscount,
                        'customer_card_units_used' => $cardUnitsUsed,
                        'customer_card_coverage_type' => $cardCoverageTypeValue,
                        'customer_card_unit_price' => $cardUnitPrice,
                    ], HotelSupport::CONTEXT_COURSE);
                } else {
                    HotelHelper::saveCheckoutData([
                        'customer_card_id' => null,
                        'customer_card_discount' => null,
                        'customer_card_units_used' => null,
                        'customer_card_coverage_type' => null,
                        'customer_card_unit_price' => null,
                    ], HotelSupport::CONTEXT_COURSE);
                }
            }
        } else {
            HotelHelper::saveCheckoutData([
                'customer_card_id' => null,
                'customer_card_discount' => null,
                'customer_card_units_used' => null,
                'customer_card_coverage_type' => null,
                'customer_card_unit_price' => null,
            ], HotelSupport::CONTEXT_COURSE);
        }

        $totalAfterDiscountRaw = max($totalRaw - $cardDiscount, 0);
        $totalAfterDiscount = course_truncate_price($totalAfterDiscountRaw);
        $minimumOnlinePaymentFee = 0.0;

        if ($totalAfterDiscount > 0 && $totalAfterDiscount < self::MINIMUM_ONLINE_PAYMENT_AMOUNT) {
            $minimumOnlinePaymentFee = course_truncate_price(self::MINIMUM_ONLINE_PAYMENT_AMOUNT - $totalAfterDiscount);
        }

        $finalTotal = course_truncate_price($totalAfterDiscount + $minimumOnlinePaymentFee);
        $minimumOnlinePaymentThreshold = self::MINIMUM_ONLINE_PAYMENT_AMOUNT;

        return Theme::scope(
            'courses.booking',
            compact(
                'course',
                'token',
                'customer',
                'amount',
                'amountNet',
                'netSubtotal',
                'total',
                'taxAmount',
                'couponAmount',
                'couponAmountNet',
                'couponCode',
                'session',
                'basePrice',
                'discountAmount',
                'checkoutData',
                'availableCards',
                'selectedCard',
                'cardDiscount',
                'cardUnitPrice',
                'customerCardsAllowed',
                'totalAfterDiscount',
                'minimumOnlinePaymentFee',
                'finalTotal',
                'minimumOnlinePaymentThreshold',
                'priceBreakdown'
            )
        )->render();
    }

    public function postCourseCheckout(
        CourseCheckoutRequest $request,
        BaseHttpResponse $response,
        CustomerCardService $customerCardService
    )
    {
        do_action('form_extra_fields_validate', $request);

        $token = $request->input('token');

        if (!session()->has($token)) {
            if (session()->has('course_booking_transaction_id')) {
                return $response->setNextUrl(
                    route('public.course.booking.information', session('course_booking_transaction_id'))
                );
            }

            abort(404);
        }

        /** @var \Botble\Hotel\Models\Coupon|null $appliedCoupon */
        $appliedCoupon = null;

        $sessionData = HotelHelper::getCheckoutData(null, HotelSupport::CONTEXT_COURSE);
        $cardId = (int) Arr::get($sessionData, 'customer_card_id');
        $cardDiscount = (float) Arr::get($sessionData, 'customer_card_discount', 0);
        $cardUnitsUsed = max((int) Arr::get($sessionData, 'customer_card_units_used', 1), 1);
        $cardCoverageTypeValue = Arr::get($sessionData, 'customer_card_coverage_type');
        $cardCoverageType = $cardCoverageTypeValue
            ? CustomerCardCoverageType::tryFrom($cardCoverageTypeValue)
            : null;
        $customerCard = null;

        $courseId = (int) Arr::get($sessionData, 'course_id');
        $course = Course::query()->find($courseId);
        $customerCardsAllowed = (bool) ($course?->accept_customer_card);

        if (! $customerCardsAllowed) {
            $cardDiscount = 0;
            $cardUnitsUsed = 0;
            $cardCoverageType = null;
            $cardCoverageTypeValue = null;

            HotelHelper::saveCheckoutData([
                'customer_card_id' => null,
                'customer_card_discount' => null,
                'customer_card_units_used' => null,
                'customer_card_coverage_type' => null,
            ], HotelSupport::CONTEXT_COURSE);
        }

        if ($customerCardsAllowed && $cardId && Auth::guard('customer')->check()) {
            $customerCard = $customerCardService->getValidCard($cardId, Auth::guard('customer')->id());

            if (! $customerCard) {
                $cardDiscount = 0;
                $cardCoverageType = null;
                $cardCoverageTypeValue = null;
                $cardUnitsUsed = 0;
                HotelHelper::saveCheckoutData([
                    'customer_card_id' => null,
                    'customer_card_discount' => null,
                    'customer_card_units_used' => null,
                    'customer_card_coverage_type' => null,
                ], HotelSupport::CONTEXT_COURSE);
            }
        }

        try {
            DB::beginTransaction();

            $session = CourseSession::query()
                ->where('id', $request->input('session_id'))
                ->lockForUpdate()
                ->firstOrFail();

            if (!$session->hasAvailableSeats()) {
                DB::rollBack();

                return $response
                    ->setError()
                    ->setNextUrl(route('public.courses'))
                    ->setMessage(__('No seats available for this session.'));
            }

            $course = $course ?? Course::query()->findOrFail($request->input('course_id'));

            if ($request->input('register_customer') == 1) {
                $request->validate([
                    'first_name' => 'required|string|max:60|min:2',
                    'last_name' => 'required|string|max:60|min:2',
                    'email' => 'required|max:120|min:6|email|unique:ht_customers',
                    'phone' => 'required|string|'.BaseHelper::getPhoneValidationRule(),
                    'password' => 'required|string|min:6|confirmed',
                ]);

                $customer = Customer::query()->forceCreate([
                    'first_name' => BaseHelper::clean($request->input('first_name')),
                    'last_name' => BaseHelper::clean($request->input('last_name')),
                    'email' => BaseHelper::clean($request->input('email')),
                    'phone' => BaseHelper::clean($request->input('phone')),
                    'password' => Hash::make($request->input('password')),
                ]);

                Auth::guard('customer')->loginUsingId($customer->getKey());
            }

            $booking = new CourseBooking();
            $booking->fill($request->input());

            $pricing = $course->resolvePricing(Auth::guard('customer')->user());
            $courseGrossPrice = (float) ($pricing['calculated_gross'] ?? $course->getPriceWithTax($course->getCourseTotalPrice()));
            $basePrice = course_truncate_price((float) ($pricing['base_net'] ?? 0));
            $amountNetRaw = (float) ($pricing['calculated_net'] ?? 0);
            $amount = course_truncate_price($amountNetRaw);
            $discountAmount = course_truncate_price(max(0, (float) ($pricing['discount_net'] ?? 0)));

            $couponAmount = (float) Arr::get($sessionData, 'coupon_amount', 0);
            $couponCode = Arr::get($sessionData, 'coupon_code');
            $couponAmount = min($couponAmount, $amountNetRaw);
            $couponAmount = course_truncate_price($couponAmount);

            if ($couponCode) {
                $appliedCoupon = \Botble\Hotel\Models\Coupon::where('code', $couponCode)->first();
            }

            $netSubtotalRaw = max($amountNetRaw - $couponAmount, 0);
            $netSubtotal = course_truncate_price($netSubtotalRaw);
            $taxAmountRaw = $course->getTaxAmount($netSubtotalRaw);
            $taxAmount = course_truncate_price($taxAmountRaw);

            $booking->course_session_id = $request->input('session_id');
            $grossTotalRaw = $netSubtotalRaw + $taxAmountRaw;
            $grossTotal = course_truncate_price($grossTotalRaw);
            $effectiveCardDiscount = 0;

            if ($customerCard) {
                $cardEffect = app(CustomerCardPricingService::class)->calculateCardEffect(
                    $customerCard,
                    $course,
                    $grossTotalRaw
                );
                $cardDiscount = course_truncate_price(min($cardEffect->discountGross, $grossTotalRaw));
                $cardUnitsUsed = max(1, (int) $cardEffect->unitsUsed);
                $cardCoverageType = $cardEffect->coverageType;
                $cardCoverageTypeValue = $cardCoverageType->value;

                $effectiveCardDiscount = $cardDiscount;
            } else {
                $cardDiscount = 0;
                $cardUnitsUsed = 0;
                $cardCoverageType = null;
                $cardCoverageTypeValue = null;
            }

            $amountDueRaw = max($grossTotalRaw - $effectiveCardDiscount, 0);
            $amountDue = course_truncate_price($amountDueRaw);
            $minimumOnlinePaymentFee = 0.0;

            if ($amountDue > 0 && $amountDue < self::MINIMUM_ONLINE_PAYMENT_AMOUNT) {
                $minimumOnlinePaymentFee = course_truncate_price(self::MINIMUM_ONLINE_PAYMENT_AMOUNT - $amountDue);
            }

            $amountDue = course_truncate_price($amountDue + $minimumOnlinePaymentFee);

            $booking->amount = $amountDue;
            $booking->sub_total = $amount;
            $booking->status = $amountDue <= 0
                ? BookingStatusEnum::PROCESSING
                : BookingStatusEnum::AWAITING_PAYMENT;
            $booking->coupon_amount = $couponAmount;
            $booking->coupon_code = $couponCode;
            $booking->tax_amount = $taxAmount;
            $booking->rule_discount = $discountAmount;
            $booking->transaction_id = Str::upper(Str::random(32));
            $booking->booking_number = CourseBooking::generateUniqueBookingNumber();
            $booking->customer_card_id = $customerCard?->getKey();
            $booking->customer_card_discount = $effectiveCardDiscount;
            $booking->customer_card_discount_gross = $effectiveCardDiscount;
            $booking->customer_card_units_used = $customerCard ? $cardUnitsUsed : 0;
            $booking->customer_card_coverage_type = $customerCard
                ? ($cardCoverageType?->value ?? CustomerCardCoverageType::PARTIAL->value)
                : CustomerCardCoverageType::NONE->value;

            if (Auth::guard('customer')->check()) {
                $booking->customer_id = Auth::guard('customer')->id();
            }

            $booking->save();

            $bookingAddress = new \Botble\Courses\Models\CourseBookingAddress();
            $bookingAddress->fill($request->only([
                'first_name', 'last_name', 'email', 'phone', 'country', 'state', 'city', 'address', 'zip'
            ]));
            $bookingAddress->course_booking_id = $booking->getKey();
            $bookingAddress->save();

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        session()->put('course_booking_transaction_id', $booking->transaction_id);

        if ($booking->amount <= 0) {
            if ($appliedCoupon) {
                $appliedCoupon->increment('total_used');
            }

            $payment = null;

            $courseBookingService = app(CourseBookingService::class);
            $customerCardMethod = $courseBookingService->normalizePaymentChannel(PaymentMethodEnum::CUSTOMER_CARD());

            if (is_plugin_active('payment')) {
                $payment = Payment::query()->create([
                    'amount' => 0,
                    'currency' => strtoupper(get_application_currency()->title),
                    'charge_id' => $booking->transaction_id,
                    'payment_channel' => $customerCardMethod,
                    'status' => PaymentStatusEnum::COMPLETED,
                    'order_id' => $booking->getKey(),
                    'order_type' => CourseBooking::class,
                    'customer_id' => $booking->customer_id,
                    'customer_type' => Customer::class,
                ]);
            }

            if ($payment) {
                $booking->forceFill([
                    'payment_id' => $payment->getKey(),
                    'payment_method' => $courseBookingService->normalizePaymentChannel($payment->payment_channel),
                ])->save();
            } else {
                $booking->forceFill(['payment_method' => $customerCardMethod])->save();
            }

            do_action(PAYMENT_ACTION_PAYMENT_PROCESSED, [
                'amount' => (float) ($payment?->amount ?? 0),
                'currency' => strtoupper(get_application_currency()->title),
                'charge_id' => $payment?->charge_id ?? $booking->transaction_id,
                'payment_channel' => $payment?->payment_channel ?? $customerCardMethod,
                'status' => PaymentStatusEnum::COMPLETED,
                'order_id' => [$booking->getKey()],
                'order_type' => CourseBooking::class,
                'customer_id' => $booking->customer_id,
                'customer_type' => Customer::class,
            ]);

            $courseBookingService->finalizeCustomerCardUsage($booking);

            if ($token = $request->input('token')) {
                session()->forget($token);
                HotelHelper::clearCheckoutData();
            }

            return $response
                ->setNextUrl(route('public.course.booking.information', $booking->transaction_id))
                ->setMessage(__('Course Booking successfully!'));
        }

        $request->merge([
            'order_id' => [$booking->getKey()],
        ]);

        $data = [
            'error' => false,
            'message' => false,
            'amount' => $booking->amount,
            'currency' => strtoupper(get_application_currency()->title),
            'type' => $request->input('payment_method'),
            'charge_id' => null,
        ];

        if (is_plugin_active('payment')) {
            session()->put('selected_payment_method', $data['type']);
            session(['order_type' => \Botble\Courses\Models\CourseBooking::class]);
            $paymentData = apply_filters(PAYMENT_COURSE_FILTER_PAYMENT_DATA, [], $request);
            $paymentData['order_type'] = \Botble\Courses\Models\CourseBooking::class;

            switch ($request->input('payment_method')) {
                case PaymentMethodEnum::COD:
                    $codPaymentService = app(CodPaymentService::class);
                    $data['charge_id'] = $codPaymentService->execute($paymentData);
                    $data['message'] = trans('plugins/payment::payment.payment_pending');
                    break;

                case PaymentMethodEnum::BANK_TRANSFER:
                    $bankTransferPaymentService = app(BankTransferPaymentService::class);
                    $data['charge_id'] = $bankTransferPaymentService->execute($paymentData);
                    $data['message'] = trans('plugins/payment::payment.payment_pending');
                    break;

                default:
                    $data = apply_filters(PAYMENT_FILTER_AFTER_POST_CHECKOUT, $data, $request);
                    break;
            }

            if ($checkoutUrl = Arr::get($data, 'checkoutUrl')) {
                if ($appliedCoupon) {
                    $appliedCoupon->increment('total_used');
                }

                return $response
                    ->setError($data['error'])
                    ->setNextUrl($checkoutUrl)
                    ->setData(['checkoutUrl' => $checkoutUrl])
                    ->withInput()
                    ->setMessage($data['message']);
            }

            if ($data['error'] || !$data['charge_id']) {
                return $response
                    ->setError()
                    ->setNextUrl(route('public.course.booking.form', $token))
                    ->withInput()
                    ->setMessage($data['message'] ?: __('Checkout error!'));
            }

            $redirectUrl = route('public.course.booking.information', $booking->transaction_id);
        } else {
            $redirectUrl = route('public.course.booking.information', $booking->transaction_id);
        }

        if ($token = $request->input('token')) {
            session()->forget($token);
            HotelHelper::clearCheckoutData();
        }

        if ($appliedCoupon) {
            $appliedCoupon->increment('total_used');
        }

        return $response
            ->setNextUrl($redirectUrl)
            ->setMessage(__('Course Booking successfully!'));
    }

    public function checkoutCourseSuccess(string $transactionId)
    {
        session()->forget('order_type');

        $booking = CourseBooking::query()
            ->where('transaction_id', $transactionId)
            ->firstOrFail();

        SeoHelper::setTitle(__('Course Booking Information'));

        Theme::breadcrumb()
            ->add(__('Booking'), route('public.course.booking.information', $transactionId));

        return Theme::scope('courses.booking-information', compact('booking'))->render();
    }

    public function changeCurrency(
        Request $request,
        BaseHttpResponse $response,
        $title = null
    ) {
        if (empty($title)) {
            $title = $request->input('currency');
        }

        if (! $title) {
            return $response;
        }

        $currency = Currency::query()
            ->where('title', $title)
            ->first();

        if ($currency) {
            cms_currency()->setApplicationCurrency($currency);
        }

        return $response;
    }

    public function ajaxCalculateBookingAmount(
        CalculateBookingAmountRequest $request,
        BaseHttpResponse $response,
        CourseCheckoutStateService $checkoutStateService
    ) {
        $course = Course::query()->findOrFail($request->input('course_id'));

        $state = $checkoutStateService->buildState($course, $request->input('coupon_code'));

        return $response->setData($state);
    }


    protected function calculateDynamicPrice(
        float $basePrice,
        string $targetType,
        int $targetId,
        ?Customer $customer = null,
        int $quantity = 1
    ): float {
        if (! function_exists('is_plugin_active') || ! is_plugin_active('price-configurator')) {
            return $basePrice;
        }

        $serviceClass = 'Botble\\PriceConfigurator\\Services\\PriceConfiguratorService';

        if (! class_exists($serviceClass)) {
            return $basePrice;
        }

        try {
            $service = app($serviceClass);

            return $service->calculatePrice($basePrice, $targetType, $targetId, $customer, $quantity);
        } catch (Throwable) {
            return $basePrice;
        }
    }
}

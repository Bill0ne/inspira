<?php

namespace Botble\Courses\Http\Controllers;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Courses\DataTransferObjects\CourseSearchParams;
use Botble\Courses\Services\GetCourseService;
use Botble\Hotel\Enums\BookingStatusEnum;
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
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Botble\Hotel\Services\CouponService;
use Botble\Courses\Http\Requests\InitBookingRequest;
use Botble\Courses\Http\Requests\CalculateBookingAmountRequest;
use Botble\Payment\Supports\PaymentHelper;
use Botble\Payment\Services\Gateways\BankTransferPaymentService;
use Botble\Payment\Services\Gateways\CodPaymentService;
use Botble\Base\Facades\BaseHelper;
use Botble\Courses\Http\Requests\CourseCheckoutRequest;
use Botble\Payment\Enums\PaymentMethodEnum;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Throwable;

class PublicController extends Controller
{
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
            'checkout_token' => $token,
        ]);

        return $response->setNextUrl(route('public.course.booking.form', $token));
    }

    public function getCourseBooking(string $token, BaseHttpResponse $response)
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

        $basePrice = $course->getCourseTotalPrice();
        $amount = $basePrice;
        if (is_plugin_active('price-configurator')) {
            $amount = app(\Botble\PriceConfigurator\Services\PriceConfiguratorService::class)
                ->calculatePrice(
                    $basePrice,
                    \Botble\PriceConfigurator\Enums\TargetTypeEnum::COURSE,
                    $course->id,
                    $customer
                );
        }
        $discountAmount = $basePrice - $amount;

        $taxAmount = $course->tax->percentage * $amount / 100;
        $couponAmount = Arr::get($sessionData, 'coupon_amount', 0);
        $couponCode = Arr::get($sessionData, 'coupon_code');

        $total = $amount + $taxAmount - $couponAmount;

        return Theme::scope(
            'courses.booking',
            compact(
                'course',
                'token',
                'customer',
                'amount',
                'total',
                'taxAmount',
                'couponAmount',
                'couponCode',
                'session',
                'basePrice',
                'discountAmount'
            )
        )->render();
    }

    public function postCourseCheckout(CourseCheckoutRequest $request, BaseHttpResponse $response)
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

            $course = Course::query()->findOrFail($request->input('course_id'));

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

            $basePrice = $course->getCourseTotalPrice();
            $amount = $basePrice;
            if (is_plugin_active('price-configurator')) {
                $amount = app(\Botble\PriceConfigurator\Services\PriceConfiguratorService::class)
                    ->calculatePrice(
                        $basePrice,
                        \Botble\PriceConfigurator\Enums\TargetTypeEnum::COURSE,
                        $course->id,
                        Auth::guard('customer')->user() ?? null
                    );
            }

            $discountAmount = abs($basePrice - $amount);

            $sessionData = HotelHelper::getCheckoutData();
            $couponAmount = Arr::get($sessionData, 'coupon_amount', 0);
            $couponCode = Arr::get($sessionData, 'coupon_code');

            $taxAmount = $course->tax->percentage * ($amount - $couponAmount) / 100;

            $booking->course_session_id = $request->input('session_id');
            $booking->amount = ($amount - $couponAmount) + $taxAmount;
            $booking->sub_total = $amount;
            $booking->status = BookingStatusEnum::AWAITING_PAYMENT;
            $booking->coupon_amount = $couponAmount;
            $booking->coupon_code = $couponCode;
            $booking->tax_amount = $taxAmount;
            $booking->rule_discount = $discountAmount;
            $booking->transaction_id = Str::upper(Str::random(32));
            $booking->booking_number = CourseBooking::generateUniqueBookingNumber();

            if (Auth::guard('customer')->check()) {
                $booking->customer_id = Auth::guard('customer')->id();
            }

            $booking->save();

            if ($couponCode) {
                $coupon = \Botble\Hotel\Models\Coupon::where('code', $couponCode)->first();
                if ($coupon) {
                    $coupon->increment('total_used', 1);
                }
            }

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
            session()->forget('checkout_token');
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
        BaseHttpResponse $response
    ) {
        $course = Course::query()->findOrFail($request->input('course_id'));

        [$amount, $discountAmount] = $this->calculateBookingAmount($course);

        $customer = Auth::guard('customer')->user();
        $basePrice = $course->getCourseTotalPrice();
        $amount = $basePrice;
        if (is_plugin_active('price-configurator')) {
            $amount = app(\Botble\PriceConfigurator\Services\PriceConfiguratorService::class)
                ->calculatePrice(
                    $basePrice,
                    \Botble\PriceConfigurator\Enums\TargetTypeEnum::COURSE,
                    $course->id,
                    $customer
                );
        }

        $taxAmount = $course->tax->percentage * ($amount - $discountAmount) / 100;
        $totalAmount = ($amount - $discountAmount) + $taxAmount;

        return $response->setData([
            'total_amount'      => format_price($totalAmount),
            'amount_raw'        => $totalAmount,
            'sub_total'         => format_price($amount),
            'tax_amount'        => format_price($taxAmount),
            'discount_amount'   => format_price($discountAmount),
        ]);
    }

    protected function calculateBookingAmount(Course $course): array
    {
        $amount = $course->getCourseTotalPrice();

        $sessionData = HotelHelper::getCheckoutData();

        $couponCode = Arr::get($sessionData, 'coupon_code');
        $discountAmount = 0;

        if ($couponCode) {
            $couponService = new CouponService();
            $coupon = $couponService->getCouponByCode($couponCode);

            if ($coupon !== null) {
                $discountAmount = $couponService->getDiscountAmount(
                    $coupon->type->getValue(),
                    $coupon->value,
                    $amount
                );
            }

            $sessionData['coupon_amount'] = $discountAmount;
            $sessionData['coupon_code'] = $couponCode;
        }

        HotelHelper::saveCheckoutData($sessionData);

        return [$amount, $discountAmount];
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

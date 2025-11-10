<?php

namespace Botble\Hotel\Http\Controllers;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\Html;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Hotel\DataTransferObjects\RoomSearchParams;
use Botble\Hotel\Enums\BookingStatusEnum;
use Botble\Hotel\Enums\ReviewStatusEnum;
use Botble\Hotel\Facades\HotelHelper;
use Botble\Hotel\Http\Requests\CalculateBookingAmountRequest;
use Botble\Hotel\Http\Requests\CheckoutRequest;
use Botble\Hotel\Http\Requests\InitBookingRequest;
use Botble\Hotel\Models\Booking;
use Botble\Hotel\Models\BookingAddress;
use Botble\Hotel\Models\BookingRoom;
use Botble\Hotel\Models\Currency;
use Botble\Hotel\Models\Customer;
use Botble\Hotel\Models\Food;
use Botble\Hotel\Models\Place;
use Botble\Hotel\Models\Room;
use Botble\Hotel\Models\RoomCategory;
use Botble\Hotel\Models\Service;
use Botble\Hotel\Services\CheckoutPricingService;
use Botble\Hotel\Services\GetRoomService;
use Botble\Media\Facades\RvMedia;
use Botble\Optimize\Facades\OptimizerHelper;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Services\Gateways\BankTransferPaymentService;
use Botble\Payment\Services\Gateways\CodPaymentService;
use Botble\Payment\Supports\PaymentHelper;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\SeoHelper\SeoOpenGraph;
use Botble\Slug\Facades\SlugHelper;
use Botble\Theme\Facades\Theme;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PublicController extends Controller
{
    public function __construct(
        protected GetRoomService $getRoomService,
        protected CheckoutPricingService $checkoutPricingService
    ) {
    }

    public function getRooms(Request $request, BaseHttpResponse $response)
    {
        SeoHelper::setTitle(__('Rooms'));

        Theme::breadcrumb()->add(__('Rooms'), route('public.rooms'));

        if ($request->ajax() && $request->wantsJson()) {
            $params = RoomSearchParams::fromRequest($request->input());

            $rooms = $this->getRoomService->getAvailableRooms($params);

            $data = null;
            foreach ($rooms as $room) {
                $data = view(
                    Theme::getThemeNamespace('views.hotel.includes.room-item'),
                    compact('room')
                )->render();
            }

            return $response->setData($data);
        }

        return Theme::scope('hotel.rooms')->render();
    }

    public function getRoom(string $key)
    {
        $slug = SlugHelper::getSlug($key, SlugHelper::getPrefix(Room::class));

        abort_unless($slug, 404);

        [$startDate, $endDate, $adults] = HotelHelper::getRoomBookingParams();

        $room = Room::query()
            ->with([
                'amenities',
                'currency',
                'category',
                'activeRoomDates' => function ($query) use ($startDate, $endDate) {
                    return $query
                        ->where('start_date', '>=', $startDate)
                        ->where('end_date', '<=', $endDate)
                        ->take(42);
                },
            ])
            ->withCount([
                'reviews',
                'reviews as approved_review_count' => function (Builder $query): void {
                    $query->where('status', ReviewStatusEnum::APPROVED);
                },
            ])
            ->withAvg('reviews', 'star')
            ->findOrFail($slug->reference_id);

        SeoHelper::setTitle($room->name)->setDescription(Str::words($room->description, 120));

        $meta = new SeoOpenGraph();
        if ($room->image) {
            $meta->setImage(RvMedia::getImageUrl($room->image));
        }
        $meta->setDescription($room->description);
        $meta->setUrl($room->url);
        $meta->setTitle($room->name);
        $meta->setType('article');

        SeoHelper::setSeoOpenGraph($meta);

        Theme::breadcrumb()
            ->add($room->name, $room->url);

        if (function_exists('admin_bar')) {
            admin_bar()->registerLink(__('Edit this room'), route('room.edit', $room->getKey()));
        }

        $condition = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'adults' => $adults,
        ];

        $relatedRooms = $this->getRoomService->getRelatedRooms(
            $room->getKey(),
            (int) theme_option('number_of_related_rooms', 4),
            [
                'with' => [
                    'amenities',
                    'slugable',
                    'activeBookingRooms' => function ($query) use ($startDate, $endDate) {
                        return $query
                            ->whereNot('status', BookingStatusEnum::CANCELLED)
                            ->where(function ($query) use ($endDate, $startDate) {
                                return $query
                                    ->where('start_date', '<=', $startDate)
                                    ->where('end_date', '>=', $endDate);
                            });
                    },
                    'activeRoomDates' => function ($query) use ($startDate, $endDate) {
                        return $query
                            ->where('start_date', '>=', $startDate)
                            ->where('end_date', '<=', $endDate)
                            ->take(42);
                    },
                ],
            ]
        );

        foreach ($relatedRooms as &$relatedRoom) {
            if ($relatedRoom->isAvailableAt($condition)) {
                $relatedRoom->total_price = $relatedRoom->getRoomTotalPrice($startDate, $endDate);
            }
        }

        do_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, ROOM_MODULE_SCREEN_NAME, $room);

        $images = [];
        foreach ($room->images as $image) {
            $images[] = RvMedia::getImageUrl($image, null, false, RvMedia::getDefaultImage());
        }

        $room->total_price = $room->getRoomTotalPrice($startDate, $endDate);

        Theme::asset()->add('ckeditor-content-styles', 'vendor/core/core/base/libraries/ckeditor/content-styles.css');

        $room->content = Html::tag('div', (string) $room->content, ['class' => 'ck-content'])->toHtml();

        return Theme::scope('hotel.room', compact('room', 'images', 'relatedRooms', 'startDate', 'endDate', 'adults'))->render();
    }

    public function getRoomCategory(string $key)
    {
        $slug = SlugHelper::getSlug($key, SlugHelper::getPrefix(RoomCategory::class));

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
            ->add($category->name, $category->url);

        do_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, ROOM_MODULE_SCREEN_NAME, $category);

        $rooms = Room::query()
            ->whereHas('category', function ($query) use ($category) {
                return $query->where('id', $category->getKey());
            })
            ->wherePublished()
            ->paginate();

        return Theme::scope('hotel.room-category', compact('rooms', 'category'))->render();
    }

    public function getPlace(string $key)
    {
        $slug = SlugHelper::getSlug($key, SlugHelper::getPrefix(Place::class));

        abort_unless($slug, 404);

        $place = Place::query()
            ->with(['slugable'])
            ->findOrFail($slug->reference_id);

        SeoHelper::setTitle($place->name)->setDescription(Str::words($place->description, 120));

        $meta = new SeoOpenGraph();
        if ($place->image) {
            $meta->setImage(RvMedia::getImageUrl($place->image));
        }
        $meta->setDescription($place->description);
        $meta->setUrl($place->url);
        $meta->setTitle($place->name);
        $meta->setType('article');

        SeoHelper::setSeoOpenGraph($meta);

        Theme::breadcrumb()
            ->add($place->name, $place->url);

        $relatedPlaces = Place::query()
            ->wherePublished()
            ->whereNot('id', $place->getKey())
            ->limit(3)
            ->get();

        do_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, PLACE_MODULE_SCREEN_NAME, $place);

        Theme::asset()->add('ckeditor-content-styles', 'vendor/core/core/base/libraries/ckeditor/content-styles.css');

        $place->content = Html::tag('div', (string) $place->content, ['class' => 'ck-content'])->toHtml();

        return Theme::scope('hotel.place', compact('place', 'relatedPlaces'))->render();
    }

    public function postBooking(InitBookingRequest $request, BaseHttpResponse $response)
    {
        abort_if(!HotelHelper::isBookingEnabled(), 404);

        $room = Room::query()
            ->with(['currency', 'category'])
            ->findOrFail($request->input('room_id'));

        $rawSlots = $request->input('slots', []);

        if (empty($rawSlots)) {
            return $response
                ->setError()
                ->setMessage(__('Please select at least one booking slot.'))
                ->withInput();
        }

        $slots = [];
        $dateFormat = HotelHelper::getDateFormat(); // e.g. "d.m.Y H:i"

        foreach ($rawSlots as $slot) {
            // Example: "05.11.2025 10:00 - 11:00"
            if (!preg_match('/^(\d{2}\.\d{2}\.\d{4})\s+(\d{2}:\d{2})\s*-\s*(\d{2}:\d{2})$/', trim($slot), $matches)) {
                return $response
                    ->setError()
                    ->setMessage(__('Invalid slot format: ":slot".', ['slot' => $slot]))
                    ->withInput();
            }

            [$full, $date, $startTime, $endTime] = $matches;

            try {
                $startDate = Carbon::createFromFormat('d.m.Y H:i', "{$date} {$startTime}");
                $endDate   = Carbon::createFromFormat('d.m.Y H:i', "{$date} {$endTime}");
            } catch (\Exception $e) {
                return $response
                    ->setError()
                    ->setMessage(__('Failed to parse date for slot ":slot".', ['slot' => $slot]))
                    ->withInput();
            }

            $slots[] = [
                'start_date' => $startDate,
                'end_date'   => $endDate,
            ];
        }

        $adults = $request->integer('adults', 1);
        $children = $request->integer('children', 0);
        $rooms = $request->integer('rooms', 1);

        foreach ($slots as $index => $slot) {
            $startDate = $slot['start_date'];
            $endDate = $slot['end_date'];

            if ($endDate->lessThanOrEqualTo($startDate)) {
                return $response
                    ->setError()
                    ->setMessage(__('End date must be after start date for slot #:number.', ['number' => $index + 1]))
                    ->withInput();
            }

            // --------------------------------------------------
            // 🔹 Check if this room is attached to any course
            // --------------------------------------------------
            $attachedCourses = \Botble\Courses\Models\Course::query()->where('room_id', $room->id)
                ->with('sessions')
                ->get();

            if ($attachedCourses->isNotEmpty()) {
                foreach ($attachedCourses as $course) {
                    foreach ($course->sessions as $session) {
                        if (!$session->start_date || !$session->end_date) {
                            continue;
                        }

                        // Check for overlap between booking slot and session
                        $sessionStart = Carbon::parse($session->start_date);
                        $sessionEnd = Carbon::parse($session->end_date);

                        $overlaps =
                            $startDate->lessThan($sessionEnd) &&
                            $endDate->greaterThan($sessionStart);

                        if ($overlaps) {
                            return $response
                                ->setError()
                                ->setMessage(__('Room ":room" is unavailable from :start to :end due to a scheduled course session (":course").', [
                                    'room'   => $room->name ?? ('#' . $room->id),
                                    'course' => $course->name ?? ('Course #' . $course->id),
                                    'start'  => $sessionStart->format('d.m.Y H:i'),
                                    'end'    => $sessionEnd->format('d.m.Y H:i'),
                                ]))
                                ->withInput();
                        }
                    }
                }
            }

            // --------------------------------------------------
            // 🔹 Continue with normal room availability check
            // --------------------------------------------------
            $condition = [
                'start_date' => $startDate,
                'end_date'   => $endDate,
                'adults'     => $adults,
                'children'   => $children,
                'rooms'      => $rooms,
            ];

            if (!$room->isAvailableAt($condition)) {
                return $response
                    ->setError()
                    ->setMessage(__('This room is not available for booking from :start_date to :end_date!', [
                        'start_date' => $startDate->toDateTimeString(),
                        'end_date'   => $endDate->toDateTimeString(),
                    ]))
                    ->withInput();
            }
        }

        // --------------------------------------------------
        // 🔹 If all checks passed, continue to booking
        // --------------------------------------------------
        $token = md5(Str::random(40));

        session([
            $token => $request->except(['_token']),
            'checkout_token' => $token,
        ]);

        return $response->setNextUrl(route('public.booking.form', $token));
    }

    public function getBooking(string $token, BaseHttpResponse $response)
    {
        abort_if(!HotelHelper::isBookingEnabled(), 404);

        SeoHelper::setTitle(__('Booking'));
        OptimizerHelper::disable();

        $customer = Auth::guard('customer')->user() ?? new Customer();

        // Retrieve session data
        $sessionData = session($token, []);
        abort_if(empty($sessionData), 404);

        Theme::breadcrumb()->add(__('Booking'), route('public.booking'));

        $slots = Arr::get($sessionData, 'slots', []);
        $adults = Arr::get($sessionData, 'adults');
        $children = Arr::get($sessionData, 'children', 0);
        $rooms = Arr::get($sessionData, 'rooms', 1);

        $room = Room::query()
            ->with(['currency', 'category'])
            ->findOrFail(Arr::get($sessionData, 'room_id'));

        $pricing = $this->checkoutPricingService->calculateRoomPricing($room, $slots, (int) $rooms, $customer);

        $slotSummaries = $pricing['slots'];
        $totalConfiguredPrice = $pricing['total_configured_price'];

        $serviceAmount = Arr::get($sessionData, 'service_amount', 0);
        $foodAmount = Arr::get($sessionData, 'food_amount', 0);
        $couponAmount = Arr::get($sessionData, 'coupon_amount', 0);
        $couponCode   = Arr::get($sessionData, 'coupon_code');

        $totalRoomPrice = $totalConfiguredPrice;
        $extrasAmount = $serviceAmount + $foodAmount;
        $totalAmount = $totalRoomPrice + $extrasAmount;
        $taxAmount = $room->tax->percentage * $totalAmount / 100;
        $total = $totalAmount + $taxAmount - $couponAmount;

        $services = Service::query()->wherePublished()->get();
        $isEnabledFoodOrder = HotelHelper::isEnableFoodOrder();
        $foods = $isEnabledFoodOrder ? Food::query()->wherePublished()->get() : collect();
        $selectedServices = Arr::get($sessionData, 'selected_services', []);
        $selectedFoods = $isEnabledFoodOrder ? Arr::get($sessionData, 'selected_foods', []) : [];

        $displayStart = !empty($slotSummaries) ? collect($slotSummaries)->pluck('start_date')->filter()->sort()->first() : null;
        $displayEnd   = !empty($slotSummaries) ? collect($slotSummaries)->pluck('end_date')->filter()->sortDesc()->first() : null;

        return Theme::scope(
            'hotel.booking',
            compact(
                'room',
                'services',
                'slots',
                'slotSummaries',
                'adults',
                'children',
                'rooms',
                'totalAmount',
                'total',
                'taxAmount',
                'couponAmount',
                'couponCode',
                'customer',
                'selectedServices',
                'selectedFoods',
                'foods',
                'totalRoomPrice',
                'extrasAmount',
                'token',
                'displayStart',
                'displayEnd'
            )
        )->render();
    }


    public function postCheckout(CheckoutRequest $request, BaseHttpResponse $response)
    {
        do_action('form_extra_fields_validate', $request);

        $token = $request->input('token');

        if (!session()->has($token)) {
            if (session()->has('booking_transaction_id')) {
                return $response->setNextUrl(route('public.booking.information', session('booking_transaction_id')));
            }

            abort(404);
        }

        $room = Room::query()->findOrFail($request->input('room_id'));

        if ($request->input('register_customer') == 1) {
            $request->validate(apply_filters('hotel_customer_registration_form_validation_rules', [
                'first_name' => 'required|string|max:60|min:2',
                'last_name' => 'required|string|max:60|min:2',
                'email' => 'required|max:120|min:6|email|unique:ht_customers',
                'phone' => 'required|string|' . BaseHelper::getPhoneValidationRule(),
                'password' => 'required|string|min:6|confirmed',
            ]));

            $customer = Customer::query()->forceCreate([
                'first_name' => BaseHelper::clean($request->input('first_name')),
                'last_name' => BaseHelper::clean($request->input('last_name')),
                'email' => BaseHelper::clean($request->input('email')),
                'phone' => BaseHelper::clean($request->input('phone')),
                'password' => Hash::make($request->input('password')),
            ]);

            Auth::guard('customer')->loginUsingId($customer->getKey());
        }

        $slots = $request->input('slots', []);
        abort_if(empty($slots), 404);

        $numberOfRooms = (int) $request->input('rooms', 1);

        $sessionData = HotelHelper::getCheckoutData();
        $couponCode = Arr::get($sessionData, 'coupon_code');

        $serviceIds = $request->input('services', []);
        $foodIds = HotelHelper::isEnableFoodOrder() ? $request->input('foods', []) : [];

        $totals = $this->checkoutPricingService->calculateTotals(
            $room,
            $slots,
            $serviceIds,
            $foodIds,
            $numberOfRooms,
            Auth::guard('customer')->user(),
            $couponCode
        );

        $slotSummaries = $totals['slots'];
        abort_if(empty($slotSummaries), 404);

        $discountAmount = $totals['rule_discount'];
        $serviceAmount = $totals['service_amount'];
        $foodAmount = $totals['food_amount'];
        $totalAmount = $totals['amount'];
        $couponAmount = $totals['coupon_amount'];
        $taxAmount = $totals['tax_amount'];
        $grandTotal = $totals['total_amount'];
        $coupon = $totals['coupon'];

        if (! $coupon) {
            $couponCode = null;
            $couponAmount = 0;
        }

        HotelHelper::saveCheckoutData([
            'service_amount' => $serviceAmount,
            'selected_services' => $totals['selected_services'],
            'food_amount' => $foodAmount,
            'selected_foods' => $totals['selected_foods'],
            'coupon_amount' => $couponAmount,
            'coupon_code' => $couponCode,
        ]);

        // 🟢 Create booking record
        $booking = new Booking();
        $booking->fill($request->except(['number_of_children']));
        $booking->number_of_children = 0;
        $booking->amount = $grandTotal;
        $booking->sub_total = $totalAmount;
        $booking->tax_amount = $taxAmount;
        $booking->rule_discount = $discountAmount;
        $booking->coupon_code = $couponCode;
        $booking->coupon_amount = $couponAmount;
        $booking->transaction_id = Str::upper(Str::random(32));
        $booking->booking_number = Booking::generateUniqueBookingNumber();

        if (Auth::guard('customer')->check()) {
            $booking->customer_id = Auth::guard('customer')->id();
        }

        $booking->save();

        // 🟢 Save all slot records in booking_rooms with configured pricing per slot
        foreach ($slotSummaries as $slot) {
            BookingRoom::query()->create([
                'room_id' => $room->getKey(),
                'room_name' => $room->name,
                'room_image' => Arr::first($room->images),
                'booking_id' => $booking->getKey(),
                'price' => $slot['final_price'],
                'currency_id' => $room->currency_id,
                'number_of_rooms' => $numberOfRooms,
                'start_date' => $slot['start_date']->format('Y-m-d H:i'),
                'end_date' => $slot['end_date']->format('Y-m-d H:i'),
            ]);
        }

// 🟢 Save address and continue existing flow
        $bookingAddress = new BookingAddress();
        $bookingAddress->fill($request->input());
        $bookingAddress->booking_id = $booking->getKey();
        $bookingAddress->save();

        session()->put('booking_transaction_id', $booking->transaction_id);



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
            $paymentData['order_type'] = \Botble\Hotel\Models\Booking::class;
            $paymentData = apply_filters(PAYMENT_FILTER_PAYMENT_DATA, [], $request);
            $paymentData['order_type'] = \Botble\Hotel\Models\Booking::class;

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
                if ($coupon) {
                    $coupon->increment('total_used');
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
                    ->setNextUrl(PaymentHelper::getCancelURL())
                    ->withInput()
                    ->setMessage($data['message'] ?: __('Checkout error!'));
            }

            $redirectUrl = route('public.booking.information', $booking->transaction_id);
        } else {
            $redirectUrl = route('public.booking.information', $booking->transaction_id);
        }

        if ($token = $request->input('token')) {
            session()->forget($token);
            session()->forget('checkout_token');
        }

        if ($coupon) {
            $coupon->increment('total_used');
        }

        return $response
            ->setNextUrl($redirectUrl)
            ->setMessage(__('Booking successfully!'));
    }

    public function checkoutSuccess(string $transactionId)
    {
        $booking = Booking::query()
            ->where('transaction_id', $transactionId)
            ->firstOrFail();

        SeoHelper::setTitle(__('Booking Information'));

        Theme::breadcrumb()
            ->add(__('Booking'), route('public.booking.information', $transactionId));

        return Theme::scope('hotel.booking-information', compact('booking'))->render();
    }

    public function ajaxCalculateBookingAmount(
        CalculateBookingAmountRequest $request,
        BaseHttpResponse $response
    ) {
        $room = Room::query()->findOrFail($request->input('room_id'));
        $slots = $request->input('slots', []);

        if (empty($slots)) {
            $startDate = HotelHelper::dateFromRequest($request->input('start_date'));
            $endDate = HotelHelper::dateFromRequest($request->input('end_date'));

            if ($endDate->lessThanOrEqualTo($startDate)) {
                abort(400, 'Invalid booking dates.');
            }

            $dateFormat = HotelHelper::getDateFormat();
            $slots = [[
                'start_date' => $startDate->format($dateFormat),
                'end_date' => $endDate->format($dateFormat),
            ]];
        }

        $numberOfRooms = (int) $request->input('rooms', 1);
        $customer = Auth::guard('customer')->user();

        $sessionData = HotelHelper::getCheckoutData();
        $couponCode = Arr::get($sessionData, 'coupon_code');

        $services = $request->input('services', []);
        $foods = HotelHelper::isEnableFoodOrder() ? $request->input('foods', []) : [];

        $totals = $this->checkoutPricingService->calculateTotals(
            $room,
            $slots,
            $services,
            $foods,
            $numberOfRooms,
            $customer,
            $couponCode
        );

        $coupon = $totals['coupon'];

        HotelHelper::saveCheckoutData([
            'service_amount' => $totals['service_amount'],
            'selected_services' => $totals['selected_services'],
            'food_amount' => $totals['food_amount'],
            'selected_foods' => $totals['selected_foods'],
            'coupon_amount' => $totals['coupon_amount'],
            'coupon_code' => $coupon ? $couponCode : null,
        ]);

        return $response->setData([
            'total_amount'    => format_price($totals['total_amount']),
            'amount_raw'      => $totals['total_amount'],
            'sub_total'       => format_price($totals['amount']),
            'tax_amount'      => format_price($totals['tax_amount']),
            'discount_amount' => format_price($totals['discount_amount']),
        ]);
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

    public function getService(string $slug)
    {
        $slug = SlugHelper::getSlug($slug, SlugHelper::getPrefix(Service::class));

        abort_unless($slug, 404);

        $query = Service::query()
            ->wherePublished();

        $services = $query->get();

        $service  = $query->findOrFail($slug->reference_id);

        SeoHelper::setTitle($service->name)
            ->setDescription($service->description);

        SeoHelper::setSeoOpenGraph(
            (new SeoOpenGraph())
                ->setDescription($service->description)
                ->setUrl($service->url)
                ->setTitle($service->name)
                ->setType('article')
        );

        Theme::breadcrumb()->add($service->name, $service->url);

        return Theme::scope('hotel.service', compact('service', 'services'))->render();
    }

    public function getFood(string $slug)
    {
        $slug = SlugHelper::getSlug($slug, SlugHelper::getPrefix(Food::class));

        abort_unless($slug, 404);

        $food = $slug->reference;

        if (! $food) {
            abort(404);
        }

        SeoHelper::setTitle($food->name)
            ->setDescription($food->description);

        SeoHelper::setSeoOpenGraph(
            (new SeoOpenGraph())
                ->setDescription($food->description)
                ->setUrl($food->url)
                ->setTitle($food->name)
                ->setType('article')
        );

        Theme::breadcrumb()->add($food->name, $food->url);

        return Theme::scope('hotel.food', compact('food'))->render();
    }

}

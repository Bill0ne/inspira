<?php

namespace Botble\Hotel\Http\Controllers\Front;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Hotel\Facades\HotelHelper;
use Botble\Hotel\Models\Room;
use Botble\Hotel\Services\CheckoutPricingService;
use Botble\Hotel\Services\CouponService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;

class CouponController extends BaseController
{
    public function __construct(
        protected BaseHttpResponse $response,
        protected CheckoutPricingService $checkoutPricingService
    )
    {
        $this->middleware(function (Request $request, Closure $next) {
            abort_unless($request->ajax(), 404);

            return $next($request);
        });
    }

    public function apply(Request $request, CouponService $couponService): BaseHttpResponse
    {
        $request->validate([
            'coupon_code' => ['required', 'string'],
        ]);

        $couponCode = trim($request->input('coupon_code'));

        $coupon = $couponService->getCouponByCode($couponCode);

        if ($coupon === null) {
            return $this->response
                ->setError()
                ->setMessage(__('This coupon is invalid!'));
        }

        $sessionData = HotelHelper::getCheckoutData();
        $sessionData['coupon_code'] = $couponCode;

        $token = session('checkout_token');

        if ($token && session()->has($token)) {
            $bookingData = session($token, []);
            $roomId = Arr::get($bookingData, 'room_id');

            if ($roomId) {
                $room = Room::query()->find($roomId);

                if ($room) {
                    $slots = Arr::get($bookingData, 'slots', []);
                    $numberOfRooms = (int) Arr::get($bookingData, 'rooms', 1);
                    $services = Arr::get($sessionData, 'selected_services', []);
                    $foods = HotelHelper::isEnableFoodOrder() ? Arr::get($sessionData, 'selected_foods', []) : [];

                    $totals = $this->checkoutPricingService->calculateTotals(
                        $room,
                        $slots,
                        $services,
                        $foods,
                        $numberOfRooms,
                        Auth::guard('customer')->user(),
                        $couponCode
                    );

                    if (! $totals['coupon']) {
                        $sessionData['coupon_code'] = null;
                        $sessionData['coupon_amount'] = 0;
                    } else {
                        $sessionData['coupon_code'] = $couponCode;
                        $sessionData['coupon_amount'] = $totals['coupon_amount'];
                    }

                    $sessionData['service_amount'] = $totals['service_amount'];
                    $sessionData['selected_services'] = $totals['selected_services'];
                    $sessionData['food_amount'] = $totals['food_amount'];
                    $sessionData['selected_foods'] = $totals['selected_foods'];
                }
            }
        }

        HotelHelper::saveCheckoutData($sessionData);

        $message = Lang::has('plugins/hotel::coupon.messages.applied')
            ? trans('plugins/hotel::coupon.messages.applied', ['code' => $couponCode])
            : __('Applied coupon ":code" successfully!', ['code' => $couponCode]);

        return $this->response
            ->setData([
                'coupon_code' => $sessionData['coupon_code'],
                'coupon_amount' => Arr::get($sessionData, 'coupon_amount', 0),
            ])
            ->setMessage($message);
    }

    public function remove(): BaseHttpResponse
    {
        $sessionData = HotelHelper::getCheckoutData();
        $couponCode = Arr::get($sessionData, 'coupon_code');

        if (! $couponCode) {
            $message = Lang::has('plugins/hotel::coupon.messages.missing')
                ? trans('plugins/hotel::coupon.messages.missing')
                : __('This coupon is not used yet!');

            return $this->response
                ->setError()
                ->setMessage($message);
        }

        $sessionData['coupon_code'] = null;
        $sessionData['coupon_amount'] = 0;

        $token = session('checkout_token');

        if ($token && session()->has($token)) {
            $bookingData = session($token, []);
            $roomId = Arr::get($bookingData, 'room_id');

            if ($roomId) {
                $room = Room::query()->find($roomId);

                if ($room) {
                    $slots = Arr::get($bookingData, 'slots', []);
                    $numberOfRooms = (int) Arr::get($bookingData, 'rooms', 1);
                    $services = Arr::get($sessionData, 'selected_services', []);
                    $foods = HotelHelper::isEnableFoodOrder() ? Arr::get($sessionData, 'selected_foods', []) : [];

                    $totals = $this->checkoutPricingService->calculateTotals(
                        $room,
                        $slots,
                        $services,
                        $foods,
                        $numberOfRooms,
                        Auth::guard('customer')->user(),
                        null
                    );

                    $sessionData['service_amount'] = $totals['service_amount'];
                    $sessionData['selected_services'] = $totals['selected_services'];
                    $sessionData['food_amount'] = $totals['food_amount'];
                    $sessionData['selected_foods'] = $totals['selected_foods'];
                    $sessionData['coupon_amount'] = $totals['coupon_amount'];
                }
            }
        }

        HotelHelper::saveCheckoutData($sessionData);

        $message = Lang::has('plugins/hotel::coupon.messages.removed')
            ? trans('plugins/hotel::coupon.messages.removed', ['code' => $couponCode])
            : __('Removed coupon ":code" successfully!', ['code' => $couponCode]);

        return $this->response
            ->setMessage($message);
    }

    public function refresh(): BaseHttpResponse
    {
        return $this->response
            ->setData(view('plugins/hotel::coupons.partials.form')->render());
    }
}

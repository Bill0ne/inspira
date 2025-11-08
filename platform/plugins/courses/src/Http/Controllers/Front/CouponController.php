<?php

namespace Botble\Courses\Http\Controllers\Front;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Hotel\Facades\HotelHelper;
use Botble\Hotel\Services\CouponService;
use Illuminate\Http\Request;
use Closure;

class CouponController extends BaseController
{
    public function __construct(protected BaseHttpResponse $response)
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

        $couponCode = $request->input('coupon_code');
        $coupon = $couponService->getCouponByCode($couponCode);

        if ($coupon === null) {
            return $this->response
                ->setError()
                ->setMessage(__('This coupon is invalid!'));
        }

        HotelHelper::saveCheckoutData([
            'coupon_code' => $couponCode,
        ]);

        return $this->response
            ->setMessage(__('Applied coupon ":code" successfully!', ['code' => $couponCode]));
    }

    public function remove(): BaseHttpResponse
    {
        $couponCode = HotelHelper::getCheckoutData('coupon_code');

        if (! $couponCode) {
            return $this->response
                ->setError()
                ->setMessage(__('This coupon is not used yet!'));
        }

        // Coupon löschen
        HotelHelper::saveCheckoutData([
            'coupon_code' => null,
            'coupon_amount' => 0,
        ]);

        // 🔹 aktuelle Beträge neu berechnen
        $price = HotelHelper::getCartAmount(true); // netto
        $tax   = HotelHelper::getTaxAmount();      // steuer
        $total = $price + $tax;                    // brutto

        // Neues HTML-Formular rendern
        $html = view('plugins/courses::coupons.partials.form')->render();

        return $this->response
            ->setMessage(__('Removed coupon :code successfully!', ['code' => $couponCode]))
            ->setData([
                'price'    => $price,
                'discount' => 0,
                'tax'      => $tax,
                'total'    => $total,
                'html'     => $html,
            ]);
    }

    public function refresh(): BaseHttpResponse
    {
        return $this->response
            ->setData(view('plugins/courses::coupons.partials.form')->render());
    }
}

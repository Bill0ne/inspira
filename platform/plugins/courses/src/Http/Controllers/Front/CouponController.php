<?php

namespace Botble\Courses\Http\Controllers\Front;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Courses\Models\Course;
use Botble\Hotel\Facades\HotelHelper;
use Botble\Hotel\Services\CouponService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
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
            'course_id' => ['nullable', 'integer'],
        ]);

        $couponCode = trim((string) $request->input('coupon_code'));
        $coupon = $couponService->getCouponByCode($couponCode);

        if ($coupon === null) {
            return $this->response
                ->setError()
                ->setMessage(__('This coupon is invalid!'));
        }

        [$course, $sessionData] = $this->resolveCourseFromCheckout($request->input('course_id'));

        if (! $course) {
            return $this->response
                ->setError()
                ->setMessage(__('Wir konnten den Kurs für diese Buchung nicht ermitteln. Bitte versuche es erneut.'));
        }

        $pricing = $course->resolvePricing(Auth::guard('customer')->user());
        $amountNet = (float) Arr::get($pricing, 'calculated_net', 0);

        $discountAmount = $couponService->getDiscountAmount(
            $coupon->type->getValue(),
            (float) $coupon->value,
            $amountNet
        );

        $discountAmount = min($discountAmount, $amountNet);
        $netSubtotal = max($amountNet - $discountAmount, 0);
        $taxAmount = $course->getTaxAmount($netSubtotal);
        $totalAmount = $netSubtotal + $taxAmount;
        $subTotalDisplay = $course->getPriceWithTax($amountNet);
        $couponDisplay = $course->getPriceWithTax($discountAmount);
        $discountDisplay = $discountAmount > 0
            ? '-' . format_price($couponDisplay)
            : format_price(0);

        $sessionData['coupon_code'] = $couponCode;
        $sessionData['coupon_amount'] = $discountAmount;
        $sessionData['course_id'] = $course->getKey();

        HotelHelper::saveCheckoutData($sessionData);

        return $this->response
            ->setData([
                'sub_total' => format_price($subTotalDisplay),
                'discount_amount' => $discountDisplay,
                'tax_amount' => format_price($taxAmount),
                'total_amount' => format_price($totalAmount),
                'amount_raw' => $totalAmount,
                'coupon_view' => view('plugins/courses::coupons.partials.form', compact('course'))->render(),
            ])
            ->setMessage(__('Applied coupon ":code" successfully!', ['code' => $couponCode]));
    }

    public function remove(Request $request): BaseHttpResponse
    {
        [$course, $sessionData] = $this->resolveCourseFromCheckout($request->input('course_id'));
        $couponCode = Arr::get($sessionData, 'coupon_code');

        if (! $couponCode) {
            return $this->response
                ->setError()
                ->setMessage(__('This coupon is not used yet!'));
        }

        $sessionData['coupon_code'] = null;
        $sessionData['coupon_amount'] = 0;

        HotelHelper::saveCheckoutData($sessionData);

        $data = null;

        if ($course) {
            $pricing = $course->resolvePricing(Auth::guard('customer')->user());
            $amountNet = (float) Arr::get($pricing, 'calculated_net', 0);
            $netSubtotal = $amountNet;
            $taxAmount = $course->getTaxAmount($netSubtotal);
            $totalAmount = $netSubtotal + $taxAmount;
            $subTotalDisplay = $course->getPriceWithTax($amountNet);

            $data = [
                'sub_total' => format_price($subTotalDisplay),
                'discount_amount' => format_price(0),
                'tax_amount' => format_price($taxAmount),
                'total_amount' => format_price($totalAmount),
                'amount_raw' => $totalAmount,
                'coupon_view' => view('plugins/courses::coupons.partials.form', compact('course'))->render(),
            ];
        }

        return $this->response
            ->setData($data)
            ->setMessage(__('Removed coupon ":code" successfully!', ['code' => $couponCode]));
    }

    public function refresh(): BaseHttpResponse
    {
        [$course] = $this->resolveCourseFromCheckout();

        return $this->response
            ->setData(view('plugins/courses::coupons.partials.form', compact('course'))->render());
    }

    protected function resolveCourseFromCheckout(?int $courseId = null): array
    {
        $sessionData = HotelHelper::getCheckoutData();

        if (! $courseId) {
            $courseId = (int) Arr::get($sessionData, 'course_id');
        }

        $course = null;

        if ($courseId) {
            $course = Course::query()->find($courseId);
        }

        if ($course && ! $sessionData) {
            $sessionData = [];
        }

        return [$course, is_array($sessionData) ? $sessionData : []];
    }
}

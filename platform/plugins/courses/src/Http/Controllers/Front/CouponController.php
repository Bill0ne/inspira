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
        $priceBreakdown = course_price_breakdown($course, Auth::guard('customer')->user());
        $amountNetRaw = (float) Arr::get($pricing, 'calculated_net', 0);

        $discountAmount = $couponService->getDiscountAmount(
            $coupon->type->getValue(),
            (float) $coupon->value,
            $amountNetRaw
        );

        $discountAmount = min($discountAmount, $amountNetRaw);
        $discountAmount = course_truncate_price($discountAmount);
        $netSubtotalRaw = max($amountNetRaw - $discountAmount, 0);
        $netSubtotal = course_truncate_price($netSubtotalRaw);
        $taxAmountRaw = $course->getTaxAmount($netSubtotalRaw);
        $taxAmount = course_truncate_price($taxAmountRaw);
        $totalAmount = course_truncate_price($netSubtotalRaw + $taxAmountRaw);
        $subTotalDisplay = course_truncate_price($course->getPriceWithTax($amountNetRaw));
        $couponDisplay = course_truncate_price($course->getPriceWithTax($discountAmount));
        $discountDisplay = $discountAmount > 0
            ? '-' . course_format_price($couponDisplay)
            : course_format_price(0);
        $displayTaxAmount = course_format_price($priceBreakdown['calculated_tax'] ?? 0);
        $displaySubTotal = course_format_price($subTotalDisplay);
        $displayTotal = course_format_price($totalAmount);

        $sessionData['coupon_code'] = $couponCode;
        $sessionData['coupon_amount'] = $discountAmount;
        $sessionData['course_id'] = $course->getKey();

        HotelHelper::saveCheckoutData($sessionData);

        return $this->response
            ->setData([
                'sub_total' => $displaySubTotal,
                'discount_amount' => $discountDisplay,
                'tax_amount' => $displayTaxAmount,
                'total_amount' => $displayTotal,
                'amount_raw' => $totalAmount,
                'coupon_code' => $couponCode,
                'coupon_view' => view('plugins/courses::coupons.partials.form', [
                    'course' => $course,
                    'appliedCouponCode' => $couponCode,
                    'appliedCouponAmount' => $discountAmount,
                ])->render(),
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

        $data = [
            'coupon_code' => null,
        ];

        if ($course) {
            $pricing = $course->resolvePricing(Auth::guard('customer')->user());
            $priceBreakdown = course_price_breakdown($course, Auth::guard('customer')->user());
            $amountNetRaw = (float) Arr::get($pricing, 'calculated_net', 0);
            $netSubtotalRaw = $amountNetRaw;
            $netSubtotal = course_truncate_price($netSubtotalRaw);
            $taxAmountRaw = $course->getTaxAmount($netSubtotalRaw);
            $taxAmount = course_truncate_price($taxAmountRaw);
            $totalAmount = course_truncate_price($netSubtotalRaw + $taxAmountRaw);
            $subTotalDisplay = course_truncate_price($course->getPriceWithTax($amountNetRaw));

            $data = array_merge($data, [
                'sub_total' => course_format_price($subTotalDisplay),
                'discount_amount' => course_format_price(0),
                'tax_amount' => course_format_price($priceBreakdown['calculated_tax'] ?? 0),
                'total_amount' => course_format_price($totalAmount),
                'amount_raw' => $totalAmount,
                'coupon_view' => view('plugins/courses::coupons.partials.form', [
                    'course' => $course,
                    'appliedCouponCode' => null,
                    'appliedCouponAmount' => 0,
                ])->render(),
            ]);
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

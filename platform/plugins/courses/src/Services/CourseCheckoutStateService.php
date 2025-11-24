<?php

namespace Botble\Courses\Services;

use Botble\Courses\Models\Course;
use Botble\Hotel\Enums\CustomerCardCoverageType;
use Botble\Hotel\Facades\HotelHelper;
use Botble\Hotel\Services\CouponService;
use Botble\Hotel\Services\CustomerCardPricingService;
use Botble\Hotel\Services\CustomerCardService;
use Botble\Hotel\Supports\HotelSupport;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class CourseCheckoutStateService
{
    protected const MINIMUM_ONLINE_PAYMENT_AMOUNT = 0.9;

    public function __construct(
        protected CouponService $couponService,
        protected CustomerCardService $customerCardService,
        protected CustomerCardPricingService $customerCardPricingService
    ) {
    }

    public function buildState($courseOrRequest, ?string $couponCode = null): array
    {
        $course = $courseOrRequest;

        if ($courseOrRequest instanceof Request) {
            $courseId = $courseOrRequest->input('course_id');
            $course = $courseId ? Course::query()->find($courseId) : null;
            $couponCode ??= $courseOrRequest->input('coupon_code');
        }

        if (! $course) {
            return [
                'success' => false,
                'card' => null,
                'coupon' => null,
                'totals' => [],
            ];
        }

        [$amountNetRaw, $couponAmountNet] = $this->calculateBookingAmount($course, $couponCode);

        $amountNetRaw = (float) $amountNetRaw;
        $couponAmountNet = min($couponAmountNet, $amountNetRaw);
        $netSubtotalRaw = max($amountNetRaw - $couponAmountNet, 0);
        $netSubtotal = course_truncate_price($netSubtotalRaw);
        $taxAmountRaw = $course->getTaxAmount($netSubtotalRaw);
        $taxAmount = course_truncate_price($taxAmountRaw);
        $totalAmountRaw = $netSubtotalRaw + $taxAmountRaw;
        $totalAmount = course_truncate_price($totalAmountRaw);

        $sessionData = HotelHelper::getCheckoutData(null, HotelSupport::CONTEXT_COURSE);
        $cardDiscount = 0.0;
        $cardId = (int) Arr::get($sessionData, 'customer_card_id');
        $cardUnitsUsed = max((int) Arr::get($sessionData, 'customer_card_units_used', 1), 1);
        $cardCoverageTypeValue = Arr::get($sessionData, 'customer_card_coverage_type');
        $cardCoverageType = $cardCoverageTypeValue
            ? CustomerCardCoverageType::tryFrom($cardCoverageTypeValue)
            : null;
        $selectedCard = null;

        if ($cardId && Auth::guard('customer')->check()) {
            $selectedCard = $this->customerCardService->getValidCard($cardId, Auth::guard('customer')->id());
        }

        if ($selectedCard) {
            $storedDiscount = (float) Arr::get($sessionData, 'customer_card_discount', 0);
            $cardEffect = $this->customerCardPricingService->calculateCardEffect(
                $selectedCard,
                $course,
                $totalAmountRaw
            );

            $cardDiscount = min($storedDiscount ?: $cardEffect->discountGross, $totalAmountRaw);
            $cardDiscount = course_truncate_price($cardDiscount);
            $cardUnitsUsed = $cardEffect->unitsUsed;
            $cardCoverageType = $cardEffect->coverageType;
            $cardCoverageTypeValue = $cardCoverageType->value;

            HotelHelper::saveCheckoutData([
                'customer_card_id' => $selectedCard->getKey(),
                'customer_card_discount' => $cardDiscount,
                'customer_card_units_used' => $cardUnitsUsed,
                'customer_card_coverage_type' => $cardCoverageTypeValue,
            ], HotelSupport::CONTEXT_COURSE);
        } else {
            if ($cardId || Arr::has($sessionData, 'customer_card_discount')) {
                HotelHelper::saveCheckoutData([
                    'customer_card_id' => null,
                    'customer_card_discount' => null,
                    'customer_card_units_used' => null,
                    'customer_card_coverage_type' => null,
                ], HotelSupport::CONTEXT_COURSE);
            }

            $cardDiscount = 0.0;
            $cardUnitsUsed = 0;
            $cardCoverageType = null;
            $cardCoverageTypeValue = null;
        }

        $totalAfterDiscountRaw = max($totalAmountRaw - $cardDiscount, 0);
        $totalAfterDiscount = course_truncate_price($totalAfterDiscountRaw);
        $minimumOnlinePaymentFee = 0.0;

        if (
            $totalAfterDiscount > 0
            && $totalAfterDiscount < self::MINIMUM_ONLINE_PAYMENT_AMOUNT
        ) {
            $minimumOnlinePaymentFee = course_truncate_price(self::MINIMUM_ONLINE_PAYMENT_AMOUNT - $totalAfterDiscount);
        }

        $finalTotal = course_truncate_price($totalAfterDiscount + $minimumOnlinePaymentFee);
        $priceBreakdown = course_price_breakdown($course, Auth::guard('customer')->user());

        $subTotalDisplay = course_truncate_price($course->getPriceWithTax($amountNetRaw));
        $couponDisplay = course_truncate_price($course->getPriceWithTax($couponAmountNet));
        $discountDisplay = $couponAmountNet > 0
            ? '-' . course_format_price($couponDisplay)
            : course_format_price(0);

        $cardDiscountDisplay = $cardDiscount > 0
            ? '-' . course_format_price($cardDiscount)
            : course_format_price(0);

        $cardDiscountPlain = $cardDiscount > 0
            ? course_format_price($cardDiscount)
            : course_format_price(0);

        $minimumFeeDisplay = $minimumOnlinePaymentFee > 0
            ? course_format_price($minimumOnlinePaymentFee)
            : course_format_price(0);

        return [
            'success' => true,
            'totals' => [
                'gross' => course_truncate_price($totalAmountRaw),
                'gross_display' => course_format_price($totalAmount),
                'sub_total_display' => course_format_price($subTotalDisplay),
                'discount_display' => $discountDisplay,
                'tax_display' => course_format_price($priceBreakdown['calculated_tax'] ?? 0),
                'total_display' => course_format_price($finalTotal),
                'total_raw' => $finalTotal,
                'minimum_fee_raw' => $minimumOnlinePaymentFee,
                'minimum_fee_display' => $minimumFeeDisplay,
                'card_discount_raw' => $cardDiscount,
                'card_discount_display' => $cardDiscountDisplay,
                'card_discount_display_plain' => $cardDiscountPlain,
                'minimum_threshold' => self::MINIMUM_ONLINE_PAYMENT_AMOUNT,
                'total_before_card_raw' => course_truncate_price($totalAmountRaw),
                'coupon_discount_raw' => $couponDisplay,
                'coupon_discount_display' => $discountDisplay,
            ],
            'card' => $selectedCard ? [
                'id' => $selectedCard->getKey(),
                'units_used' => $cardUnitsUsed,
                'discount' => $cardDiscount,
                'discount_display' => $cardDiscountPlain,
                'coverage_type' => $cardCoverageTypeValue,
            ] : null,
            'coupon' => $couponCode ? [
                'code' => $couponCode,
                'discount' => $couponDisplay,
            ] : null,
            'views' => [
                'coupon_box' => view('plugins/courses::coupons.partials.form', [
                    'course' => $course,
                    'appliedCouponCode' => $couponCode,
                    'appliedCouponAmount' => $couponAmountNet,
                ])->render(),
            ],
            'sub_total' => course_format_price($subTotalDisplay),
            'discount_amount' => $discountDisplay,
            'tax_amount' => course_format_price($priceBreakdown['calculated_tax'] ?? 0),
            'total_amount' => course_format_price($finalTotal),
            'amount_raw' => $finalTotal,
            'coupon_code' => $couponCode,
        ];
    }

    public function calculateBookingAmount(Course $course, ?string $couponCode = null): array
    {
        $pricing = $course->resolvePricing(Auth::guard('customer')->user());
        $amount = (float) ($pricing['calculated_net'] ?? 0);

        $sessionData = HotelHelper::getCheckoutData(null, HotelSupport::CONTEXT_COURSE);

        if (! $couponCode) {
            $couponCode = Arr::get($sessionData, 'coupon_code');
        }

        $discountAmount = 0;

        if ($couponCode) {
            $coupon = $this->couponService->getCouponByCode($couponCode);

            if ($coupon !== null) {
                $discountAmount = $this->couponService->getDiscountAmount(
                    $coupon->type->getValue(),
                    $coupon->value,
                    $amount
                );
                $discountAmount = min($discountAmount, $amount);
                $discountAmount = course_truncate_price($discountAmount);
            }

            $sessionData['coupon_amount'] = $discountAmount;
            $sessionData['coupon_code'] = $couponCode;
        } else {
            unset($sessionData['coupon_amount'], $sessionData['coupon_code']);
        }

        if ($course->getKey()) {
            $sessionData['course_id'] = $course->getKey();
        }

        HotelHelper::saveCheckoutData($sessionData, HotelSupport::CONTEXT_COURSE);

        return [$amount, $discountAmount];
    }
}

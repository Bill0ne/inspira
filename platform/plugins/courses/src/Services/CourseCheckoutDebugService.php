<?php

namespace Botble\Courses\Services;

use Botble\Courses\Models\Course;
use Botble\Courses\Services\CourseCheckoutStateService;
use Botble\Hotel\Facades\HotelHelper;
use Botble\Hotel\Models\CustomerCard;
use Botble\Hotel\Services\CouponService;
use Botble\Hotel\Services\CustomerCardPricingService;
use Botble\Hotel\Services\CustomerCardService;
use Botble\Hotel\Supports\HotelSupport;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class CourseCheckoutDebugService
{
    public function __construct(
        protected CourseCheckoutStateService $checkoutStateService,
        protected CustomerCardService $customerCardService,
        protected CustomerCardPricingService $customerCardPricingService,
        protected CouponService $couponService
    ) {
    }

    public function buildSnapshot(Request $request): array
    {
        $sessionData = HotelHelper::getCheckoutData(null, HotelSupport::CONTEXT_COURSE);

        $courseId = $request->input('course_id') ?: Arr::get($sessionData, 'course_id');
        $course = $courseId ? Course::query()->find($courseId) : null;

        $couponCode = $request->input('coupon_code') ?: Arr::get($sessionData, 'coupon_code');

        $state = $course
            ? $this->checkoutStateService->buildState($course, $couponCode)
            : $this->checkoutStateService->buildState($request, $couponCode);

        $cardId = (int) Arr::get($sessionData, 'customer_card_id');
        $card = $cardId ? CustomerCard::query()->find($cardId) : null;

        if (! $card && $cardId && Auth::guard('customer')->check()) {
            $card = $this->customerCardService->getValidCard($cardId, Auth::guard('customer')->id());
        }

        $baseAmount = (float) Arr::get($state, 'totals.total_before_card_raw', Arr::get($state, 'totals.gross', 0));

        $cardEffect = ($card && $course)
            ? $this->customerCardPricingService->calculateCardEffect($card, $course, $baseAmount)
            : null;

        $coupon = $couponCode ? $this->couponService->getCouponByCode($couponCode) : null;

        return [
            'state' => $state,
            'debug' => [
                'context' => [
                    'course_id' => $course?->getKey(),
                    'course_title' => $course?->name,
                    'user_id' => Auth::guard('customer')->id(),
                    'admin_id' => Auth::id(),
                ],
                'session' => [
                    'raw' => $sessionData,
                    'customer_card' => Arr::only($sessionData, [
                        'customer_card_id',
                        'customer_card_discount',
                        'customer_card_units_used',
                        'customer_card_coverage_type',
                    ]),
                    'coupon' => Arr::only($sessionData, ['coupon_code', 'coupon_amount']),
                ],
                'computed' => [
                    'totals' => Arr::get($state, 'totals', []),
                    'card_effect' => $cardEffect ? [
                        'discount_gross' => $cardEffect->discountGross,
                        'units_used' => $cardEffect->unitsUsed,
                        'unit_value_gross' => $cardEffect->unitValueGross,
                        'coverage_type' => $cardEffect->coverageType->value,
                    ] : null,
                    'coupon_effect' => $coupon ? [
                        'id' => $coupon->getKey(),
                        'code' => $coupon->code,
                        'type' => $coupon->type?->getValue(),
                        'value' => $coupon->value,
                        'applied_amount' => Arr::get($state, 'totals.coupon_discount_raw'),
                    ] : null,
                ],
                'booking_preview' => [
                    'course_id' => $course?->getKey(),
                    'customer_card_id' => $card?->getKey(),
                    'customer_card_units_used' => Arr::get($sessionData, 'customer_card_units_used'),
                    'customer_card_discount' => Arr::get($sessionData, 'customer_card_discount'),
                    'coupon_code' => $couponCode,
                    'total_due' => Arr::get($state, 'totals.total_raw'),
                    'minimum_fee' => Arr::get($state, 'totals.minimum_fee_raw'),
                    'card_discount_raw' => Arr::get($state, 'totals.card_discount_raw'),
                    'coverage_type' => Arr::get($sessionData, 'customer_card_coverage_type'),
                ],
            ],
        ];
    }
}

@php
    $sessionData = \Botble\Hotel\Facades\HotelHelper::getCheckoutData();
    $couponCode = $appliedCouponCode ?? \Illuminate\Support\Arr::get($sessionData, 'coupon_code');
    $couponAmount = $appliedCouponAmount ?? \Illuminate\Support\Arr::get($sessionData, 'coupon_amount');

    if (! isset($course) || ! $course) {
        $courseId = \Illuminate\Support\Arr::get($sessionData, 'course_id');

        if ($courseId) {
            $course = \Botble\Courses\Models\Course::query()->find($courseId);
        }
    }
@endphp
<div class="order-detail-box coupon-box checkout-action-card" data-refresh-url="{{ route('coupon.course.refresh') }}" @if(isset($course) && $course) data-course-id="{{ $course->getKey() }}" @endif>
    <div class="checkout-action-card__header">
        <div>
            <span class="checkout-action-card__eyebrow">{{ __('Gutschein') }}</span>
            <h5 class="checkout-action-card__title">{{ __('Gutscheincode einlösen') }}</h5>
        </div>
        <button class="checkout-action-toggle toggle-coupon-form" type="button">{{ trans('plugins/hotel::coupon.toggle_coupon_form_text') }}</button>
    </div>

    @php
        $hasCoupon = $couponCode && (float) $couponAmount > 0;
    @endphp
    <div class="coupon-form mt-3 checkout-action-form" @style(['display: none' => ! $hasCoupon])>
        @if ($hasCoupon)
            <div class="coupon-feedback checkout-action-feedback mb-0">
                <div class="d-flex flex-column gap-1">
                    <span class="fw-semibold">{{ __('Coupon erfolgreich angewendet') }}</span>
                    <span>{{ __('Coupon code: :code', ['code' => $couponCode]) }}</span>
                </div>
                <input name="coupon_hidden" type="hidden" value="{{ $couponCode }}" />

                <button
                    class="checkout-action-button checkout-action-button--ghost remove-coupon-code"
                    data-url="{{ route('coupon.course.remove') }}"
                    type="button"
                >
                    <x-core::icon name="ti ti-trash" />
                    {{ __('Coupon entfernen') }}
                </button>
            </div>
        @else
            <div class="form-group mb-0">
                <label for="coupon_code" class="form-label checkout-action-label">{{ trans('plugins/hotel::coupon.coupon_code') }}</label>
                <div class="checkout-action-controls checkout-action-controls--single">
                    <input type="text" id="coupon_code" name="coupon_code" class="form-control checkout-action-input" placeholder="{{ trans('plugins/hotel::coupon.coupon_code_placeholder') }}" value="{{ BaseHelper::clean(old('coupon_code')) }}">
                    <button class="checkout-action-button checkout-action-button--primary apply-coupon-code" data-url="{{ route('coupon.course.apply') }}" type="button">
                        {{ trans('plugins/hotel::coupon.apply_coupon_code') }}
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>

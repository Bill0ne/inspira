@php
    $sessionData = \Botble\Hotel\Facades\HotelHelper::getCheckoutData();
    $couponCode = \Illuminate\Support\Arr::get($sessionData, 'coupon_code');
    $couponAmount = \Illuminate\Support\Arr::get($sessionData, 'coupon_amount');

    if (! isset($course) || ! $course) {
        $courseId = \Illuminate\Support\Arr::get($sessionData, 'course_id');

        if ($courseId) {
            $course = \Botble\Courses\Models\Course::query()->find($courseId);
        }
    }
@endphp
<div class="order-detail-box coupon-box" data-refresh-url="{{ route('coupon.course.refresh') }}" @if(isset($course) && $course) data-course-id="{{ $course->getKey() }}" @endif>
    <button class="btn-link ps-0 text-decoration-none toggle-coupon-form" type="button">{{ trans('plugins/hotel::coupon.toggle_coupon_form_text') }}</button>

    <div class="coupon-form mt-3" @style(['display: none' => ! ($couponCode && $couponAmount)])>
        @if ($couponCode && $couponAmount)
            <div class="alert alert-success d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 coupon-feedback mb-0">
                <div class="d-flex flex-column">
                    <span class="fw-semibold">{{ __('Coupon erfolgreich angewendet') }}</span>
                    <span>{{ __('Coupon code: :code', ['code' => $couponCode]) }}</span>
                </div>
                <input name="coupon_hidden" type="hidden" value="{{ $couponCode }}" />

                <button
                    class="btn btn-link text-decoration-none remove-coupon-code"
                    data-url="{{ route('coupon.course.remove') }}"
                    type="button"
                >
                    <x-core::icon name="ti ti-trash" />
                    {{ __('Coupon entfernen') }}
                </button>
            </div>
        @else
            <div class="form-group mb-0">
                <label for="coupon_code" class="form-label">{{ trans('plugins/hotel::coupon.coupon_code') }}</label>
                <div class="input-group coupon-input-group">
                    <input type="text" id="coupon_code" name="coupon_code" class="form-control" placeholder="{{ trans('plugins/hotel::coupon.coupon_code_placeholder') }}" value="{{ BaseHelper::clean(old('coupon_code')) }}">
                    <button class="btn btn-primary apply-coupon-code" data-url="{{ route('coupon.course.apply') }}" type="button">
                        {{ trans('plugins/hotel::coupon.apply_coupon_code') }}
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>

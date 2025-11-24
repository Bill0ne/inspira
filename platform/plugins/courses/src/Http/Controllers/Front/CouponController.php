<?php

namespace Botble\Courses\Http\Controllers\Front;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Courses\Models\Course;
use Botble\Hotel\Facades\HotelHelper;
use Botble\Hotel\Supports\HotelSupport;
use Botble\Hotel\Services\CouponService;
use Botble\Courses\Services\CourseCheckoutStateService;
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

        $state = app(CourseCheckoutStateService::class)->buildState($course, $couponCode);

        return $this->response
            ->setData($state)
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

        HotelHelper::saveCheckoutData($sessionData, HotelSupport::CONTEXT_COURSE);

        $state = $course
            ? app(CourseCheckoutStateService::class)->buildState($course)
            : [
                'success' => true,
                'coupon' => null,
                'card' => null,
                'totals' => [],
            ];

        return $this->response
            ->setData($state)
            ->setMessage(__('Removed coupon ":code" successfully!', ['code' => $couponCode]));
    }

    public function refresh(): BaseHttpResponse
    {
        [$course] = $this->resolveCourseFromCheckout();

        $state = $course
            ? app(CourseCheckoutStateService::class)->buildState($course)
            : [
                'success' => true,
                'card' => null,
                'coupon' => null,
                'totals' => [],
            ];

        return $this->response->setData($state);
    }

    protected function resolveCourseFromCheckout(?int $courseId = null): array
    {
        $sessionData = HotelHelper::getCheckoutData(null, HotelSupport::CONTEXT_COURSE);

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

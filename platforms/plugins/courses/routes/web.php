<?php

use Botble\Base\Facades\AdminHelper;
use Botble\Courses\Http\Controllers\CourseController;
use Botble\Courses\Http\Controllers\InstructorController;
use Botble\Courses\Http\Controllers\CourseCategoryController;
use Botble\Courses\Http\Controllers\CourseSessionController;
use Botble\Courses\Http\Controllers\CourseBookingController;
use Botble\Courses\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;
use Botble\Slug\Facades\SlugHelper;
use Botble\Theme\Facades\Theme;
use Botble\Courses\Models\Course;
use Botble\Courses\Http\Controllers\Front\CouponController as CouponControllerFront;
use Botble\Courses\Models\CourseCategory;
use Botble\Courses\Facades\CourseHelper;

AdminHelper::registerRoutes(function () {
    Route::group(['prefix' => 'courses', 'as' => 'course.'], function () {
        Route::resource('', CourseController::class)->parameters(['' => 'course']);

        Route::group(['namespace' => 'Botble\Courses\Http\Controllers'], function (): void {
        Route::get('duplicate/{course}', [
            'as' => 'duplicate',
            'uses' => 'CourseController@getDuplicate',
            'permission' => 'course.create',
        ])->wherePrimaryKey('course');
        });
    });

    Route::group(['prefix' => 'course-reviews', 'as' => 'course-review.'], function (): void {
        Route::resource('', ReviewController::class)->parameters(['' => 'review'])->only(['index', 'destroy']);
    });

    Route::group(['prefix' => 'instructors', 'as' => 'instructor.'], function () {
        Route::resource('', InstructorController::class)->parameters(['' => 'instructor']);
    });

    Route::group(['prefix' => 'course-sessions', 'as' => 'course-session.'], function () {
        Route::resource('', CourseSessionController::class)->parameters(['' => 'course_session'])->only('index');
    });

    Route::group(['prefix' => 'course-categories', 'as' => 'course-category.'], function () {
        Route::resource('', CourseCategoryController::class)->parameters(['' => 'course_category']);
    });

    Route::group(['prefix' => 'course-bookings', 'as' => 'course-booking.'], function (): void {
        Route::resource('', CourseBookingController::class)->parameters(['' => 'course_booking']);
    });
});

Route::group(['namespace' => 'Botble\Courses\Http\Controllers', 'middleware' => ['web', 'core']], function (): void {

    if (defined('THEME_MODULE_SCREEN_NAME')) {
        Theme::registerRoutes(function (): void {
            Route::get(SlugHelper::getPrefix(Course::class, 'kurse'), 'PublicController@getCourses')->name('public.courses');

            Route::get(SlugHelper::getPrefix(Course::class, 'kurse') . '/{slug}', 'PublicController@getCourse');

            Route::get(SlugHelper::getPrefix(CourseCategory::class, 'kurse-kategorien') . '/{slug}', 'PublicController@getCourseCategory');

            Route::post('course/booking', 'PublicController@postCourseBooking')->name('public.course.booking');
            Route::get('course/booking/{token}', 'PublicController@getCourseBooking')->name('public.course.booking.form');

            Route::post('course/checkout', 'PublicController@postCourseCheckout')->name('public.course.booking.checkout');

            Route::get('course/checkout/{transactionId}', 'PublicController@checkoutCourseSuccess')
                ->name('public.course.booking.information');

            Route::prefix('coupon')->name('coupon.')->group(function (): void {
                Route::post('course/apply', [CouponControllerFront::class, 'apply'])->name('course.apply');
                Route::post('course/remove', [CouponControllerFront::class, 'remove'])->name('course.remove');
                Route::get('course/refresh', [CouponControllerFront::class, 'refresh'])->name('course.refresh');
            });

            Route::get('course/ajax/calculate-amount', 'PublicController@ajaxCalculateBookingAmount')
                ->name('public.course.booking.ajax.calculate-amount');

            Route::get('course/currency/switch/{code?}', [
                'as' => 'public.course.change-currency',
                'uses' => 'PublicController@changeCurrency',
            ]);

            Route::get('course/ical/{slug}', [
                'as' => 'public.course.ical',
                'uses' => 'ICalController@exportIcal',
            ]);
        });
    }
});

if (defined('THEME_MODULE_SCREEN_NAME')) {
    Theme::registerRoutes(function (): void {
        if (CourseHelper::isReviewEnabled()) {
            Route::group([
                'namespace' => 'Botble\Courses\Http\Controllers\Front',
                'middleware' => ['web', 'core'],
                'prefix' => 'customer',
                'as' => 'customer.',
            ], function (): void {
                Route::get('ajax/course/review/{key}', [
                    'as' => 'ajax.course.review.index',
                    'uses' => 'ReviewController@index',
                ]);
                Route::post('ajax/course/review/{key}', [
                    'as' => 'ajax.course.review.store',
                    'uses' => 'ReviewController@store',
                ]);
                Route::get('course-bookings', [
                    'as' => 'course-bookings',
                    'uses' => 'BookingController@index',
                ]);

            });
        }
    });
}

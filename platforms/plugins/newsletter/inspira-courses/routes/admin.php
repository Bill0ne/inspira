<?php

use Illuminate\Support\Facades\Route;
use Botble\Base\Facades\BaseHelper;
use Botble\InspiraCourses\Http\Controllers\Admin\CourseController;
use Botble\InspiraCourses\Http\Controllers\Admin\SessionController;
use Botble\InspiraCourses\Http\Controllers\Admin\InstructorController;
use Botble\InspiraCourses\Http\Controllers\Admin\PurchaseController;

Route::group([
    'prefix' => BaseHelper::getAdminPrefix() . '/inspira-courses',
    'as' => 'inspira-courses.',
    'middleware' => ['web', 'core', 'auth'],
], function () {
    Route::resource('courses', CourseController::class);
    Route::resource('sessions', SessionController::class);
    Route::resource('instructors', InstructorController::class);
    Route::resource('purchases', PurchaseController::class)->only(['index', 'show', 'destroy']);
});

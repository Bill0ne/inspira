<?php

use Illuminate\Support\Facades\Route;
use Botble\InspiraCourses\Http\Controllers\PublicController;

Route::group(['middleware' => ['web','core']], function () {
    Route::get('courses', [PublicController::class, 'index'])->name('public.courses.index');
    Route::get('courses/{slug}', [PublicController::class, 'show'])->name('public.courses.show');
    Route::post('courses/checkout', [PublicController::class, 'postCheckout'])->name('public.courses.checkout');
    Route::get('courses/success/{tx}', [PublicController::class, 'success'])->name('public.courses.success');
});

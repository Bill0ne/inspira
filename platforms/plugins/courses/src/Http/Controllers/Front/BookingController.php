<?php

namespace Botble\Courses\Http\Controllers\Front;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Hotel\Facades\InvoiceHelper;
use Botble\Courses\Models\CourseBooking;
use Botble\Hotel\Models\Invoice;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;
use Illuminate\Http\Request;


class BookingController extends BaseController
{
    public function index()
    {
        SeoHelper::setTitle(__('Course Bookings'));

        $courseBookings = CourseBooking::query()
            ->where('customer_id', auth('customer')->id())
            ->with(['course' => function($q) {
                $q->with(['instructor', 'category']);
            }])
            ->orderByDesc('created_at')
            ->paginate(5);

        Theme::breadcrumb()->add(__('Course Bookings'), route('customer.course-bookings'));

        return Theme::scope(
            'courses.bookings.list',
            compact('courseBookings'),
            'plugins/courses::themes.customers.bookings.list'
        )->render();
    }
}

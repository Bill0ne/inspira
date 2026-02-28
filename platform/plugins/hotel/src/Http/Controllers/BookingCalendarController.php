<?php

namespace Botble\Hotel\Http\Controllers;

use Botble\Base\Facades\Assets;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Courses\Models\Course;
use Botble\Hotel\Http\Requests\ManualBookingRequest;
use Botble\Hotel\Models\ManualBooking;
use Botble\Hotel\Models\Room;

class BookingCalendarController extends BaseController
{
    public function index()
    {
        $this->pageTitle(trans('plugins/hotel::booking.calendar'));

        Assets::addScriptsDirectly([
            'vendor/core/plugins/hotel/libraries/full-calendar-6.1.8/main.min.js',
            'vendor/core/plugins/hotel/js/booking-reports.js',
        ]);

        Assets::usingVueJS();

        $rooms = Room::query()->select(['id', 'name'])->orderBy('name')->get();
        $courses = Course::query()->select(['id', 'name'])->orderBy('name')->get();

        return view('plugins/hotel::booking-calendar', compact('rooms', 'courses'));
    }

    public function storeManual(ManualBookingRequest $request, BaseHttpResponse $response): BaseHttpResponse
    {
        ManualBooking::query()->create($request->validated());

        return $response
            ->setMessage(trans('plugins/hotel::booking.manual_booking_created'))
            ->setNextUrl(route('booking.calendar.index'))
            ->setPreviousUrl(route('booking.calendar.index'));
    }
}

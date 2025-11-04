<?php

namespace Botble\Courses\Http\Controllers\Front;

use Botble\Base\Http\Controllers\BaseController;
class BookingController extends BaseController
{
    public function index()
    {
        return redirect()->route('customer.bookings');
    }
}

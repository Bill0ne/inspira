<?php

namespace Botble\PriceConfigurator\Http\Controllers;

use Botble\Hotel\Http\Controllers\BookingController as BaseBookingController;
use Botble\PriceConfigurator\Services\PriceConfigurator;

class BookingController extends BaseBookingController
{
    public function __construct(
        protected PriceConfigurator $priceConfigurator
    ) {
        parent::__construct();
    }
}

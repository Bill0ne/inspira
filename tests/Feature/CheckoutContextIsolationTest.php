<?php

namespace Tests\Feature;

use Botble\Hotel\Supports\HotelSupport;
use Illuminate\Support\Str;
use Tests\TestCase;

class CheckoutContextIsolationTest extends TestCase
{
    public function test_course_and_hotel_checkout_data_are_isolated(): void
    {
        $this->withSession([]);

        $support = app(HotelSupport::class);

        $courseToken = Str::upper(Str::random(16));
        session([
            $courseToken => ['course_id' => 1],
            'course_checkout_token' => $courseToken,
            'checkout_token' => $courseToken,
            'checkout_context' => HotelSupport::CONTEXT_COURSE,
        ]);

        $support->saveCheckoutData([
            'coupon_code' => 'COURSE10',
            'customer_card_id' => 5,
        ], HotelSupport::CONTEXT_COURSE);

        $this->assertSame('COURSE10', $support->getCheckoutData('coupon_code', HotelSupport::CONTEXT_COURSE));
        $this->assertSame(5, $support->getCheckoutData('customer_card_id', HotelSupport::CONTEXT_COURSE));
        $this->assertNull($support->getCheckoutData('coupon_code', HotelSupport::CONTEXT_HOTEL));
        $this->assertNull($support->getCheckoutData('customer_card_id', HotelSupport::CONTEXT_HOTEL));

        $hotelToken = Str::upper(Str::random(16));
        session([
            $hotelToken => ['room_id' => 99],
            'hotel_checkout_token' => $hotelToken,
            'checkout_token' => $hotelToken,
            'checkout_context' => HotelSupport::CONTEXT_HOTEL,
        ]);

        $support->saveCheckoutData([
            'coupon_code' => 'HOTEL5',
            'customer_card_id' => 9,
        ], HotelSupport::CONTEXT_HOTEL);

        $this->assertSame('HOTEL5', $support->getCheckoutData('coupon_code', HotelSupport::CONTEXT_HOTEL));
        $this->assertSame(9, $support->getCheckoutData('customer_card_id', HotelSupport::CONTEXT_HOTEL));
        $this->assertSame('COURSE10', $support->getCheckoutData('coupon_code', HotelSupport::CONTEXT_COURSE));
        $this->assertSame(5, $support->getCheckoutData('customer_card_id', HotelSupport::CONTEXT_COURSE));
    }

    public function test_clearing_checkout_data_removes_both_contexts(): void
    {
        $this->withSession([]);

        $support = app(HotelSupport::class);

        $courseToken = Str::upper(Str::random(16));
        session([
            $courseToken => ['course_id' => 2],
            'course_checkout_token' => $courseToken,
            'checkout_token' => $courseToken,
            'checkout_context' => HotelSupport::CONTEXT_COURSE,
        ]);
        $support->saveCheckoutData(['coupon_code' => 'COURSE'], HotelSupport::CONTEXT_COURSE);

        $hotelToken = Str::upper(Str::random(16));
        session([
            $hotelToken => ['room_id' => 5],
            'hotel_checkout_token' => $hotelToken,
            'checkout_token' => $hotelToken,
            'checkout_context' => HotelSupport::CONTEXT_HOTEL,
        ]);
        $support->saveCheckoutData(['coupon_code' => 'HOTEL'], HotelSupport::CONTEXT_HOTEL);

        $support->clearCheckoutData();

        $this->assertFalse(session()->has($courseToken));
        $this->assertFalse(session()->has($hotelToken));
        $this->assertNull(session('course_checkout_token'));
        $this->assertNull(session('hotel_checkout_token'));
        $this->assertNull(session('checkout_token'));
        $this->assertNull(session('checkout_context'));
    }
}

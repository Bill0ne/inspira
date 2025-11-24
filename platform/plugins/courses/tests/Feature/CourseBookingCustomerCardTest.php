<?php

namespace Botble\Courses\Tests\Feature;

use Botble\ACL\Models\User;
use Botble\Courses\Models\Course;
use Botble\Courses\Models\CourseBooking;
use Botble\Courses\Models\CourseSession;
use Botble\Courses\Enums\CourseStatusEnum;
use Botble\Hotel\Facades\HotelHelper;
use Botble\Hotel\Models\Customer;
use Botble\Hotel\Models\CustomerCard;
use Botble\Hotel\Supports\HotelSupport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Tests\TestCase;

class CourseBookingCustomerCardTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_card_usage_is_recorded_for_zero_amount_course_booking(): void
    {
        $this->withSession([]);

        $admin = User::query()->create([
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => 'secret',
            'first_name' => 'Admin',
            'last_name' => 'User',
        ]);

        $customer = Customer::query()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'phone' => '+49 30 1234567',
            'password' => 'secret',
        ]);

        Auth::guard('customer')->loginUsingId($customer->getKey());

        $course = Course::query()->create([
            'name' => 'Yoga Basics',
            'price' => 50,
            'status' => CourseStatusEnum::PUBLISHED,
            'accept_customer_card' => true,
        ]);

        $session = CourseSession::query()->create([
            'course_id' => $course->getKey(),
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(2),
            'available_seats' => 5,
        ]);

        $card = CustomerCard::query()->create([
            'name' => '10er Karte',
            'type' => 'custom',
            'base_price' => 50,
            'discount_percent' => 0,
            'units_total' => 10,
            'units_remaining' => 10,
            'valid_until' => now()->addMonth(),
            'is_active' => true,
            'created_by' => $admin->getKey(),
            'assigned_to' => $customer->getKey(),
        ]);

        $token = Str::upper(Str::random(32));

        session([
            $token => [
                'course_id' => $course->getKey(),
                'session_id' => $session->getKey(),
            ],
            'course_checkout_token' => $token,
            'checkout_token' => $token,
            'checkout_context' => HotelSupport::CONTEXT_COURSE,
        ]);

        HotelHelper::saveCheckoutData([
            'customer_card_id' => $card->getKey(),
            'customer_card_units_used' => 1,
        ], HotelSupport::CONTEXT_COURSE);

        $response = $this->postJson(route('public.course.booking.checkout'), [
            'token' => $token,
            'course_id' => $course->getKey(),
            'session_id' => $session->getKey(),
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'phone' => '+49 30 1234567',
            'address' => 'Street 1',
            'city' => 'Berlin',
            'state' => 'BE',
            'country' => 'DE',
            'zip' => '10115',
            'terms_conditions' => '1',
            'amount' => 0,
            'requests' => 'N/A',
            'customer_card_id' => $card->getKey(),
        ]);

        $response->assertOk();

        $booking = CourseBooking::query()->firstOrFail();

        $this->assertSame(1, $booking->customer_card_units_used);
        $this->assertNotNull($booking->customer_card_consumed_at);

        $this->assertDatabaseHas('ht_customer_card_usages', [
            'card_id' => $card->getKey(),
            'course_id' => $course->getKey(),
            'units_used' => 1,
        ]);
    }

    public function test_course_disallows_customer_card_when_flag_is_disabled(): void
    {
        $customer = Customer::query()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john2@example.com',
            'phone' => '+49 30 9876543',
            'password' => 'secret',
        ]);

        Auth::guard('customer')->loginUsingId($customer->getKey());

        $course = Course::query()->create([
            'name' => 'Pilates',
            'price' => 75,
            'status' => CourseStatusEnum::PUBLISHED,
            'accept_customer_card' => false,
        ]);

        $session = CourseSession::query()->create([
            'course_id' => $course->getKey(),
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(2),
            'available_seats' => 5,
        ]);

        $token = Str::upper(Str::random(32));

        $this->withSession([
            $token => [
                'course_id' => $course->getKey(),
                'session_id' => $session->getKey(),
            ],
            'course_checkout_token' => $token,
            'checkout_token' => $token,
            'checkout_context' => HotelSupport::CONTEXT_COURSE,
        ])->get(route('public.course.booking.form', $token))
            ->assertOk()
            ->assertDontSee('Kundenkarte anwenden');
    }

    public function test_applying_customer_card_is_blocked_when_course_disallows_it(): void
    {
        $customer = Customer::query()->create([
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane@example.com',
            'phone' => '+49 30 1237890',
            'password' => 'secret',
        ]);

        Auth::guard('customer')->loginUsingId($customer->getKey());

        $card = CustomerCard::query()->create([
            'name' => '5er Karte',
            'type' => 'custom',
            'base_price' => 25,
            'discount_percent' => 0,
            'units_total' => 5,
            'units_remaining' => 5,
            'valid_until' => now()->addMonth(),
            'is_active' => true,
            'assigned_to' => $customer->getKey(),
        ]);

        $course = Course::query()->create([
            'name' => 'Spa',
            'price' => 100,
            'status' => CourseStatusEnum::PUBLISHED,
            'accept_customer_card' => false,
        ]);

        $response = $this->postJson(route('public.customer-card.apply'), [
            'card_id' => $card->getKey(),
            'course_id' => $course->getKey(),
            'course_checkout' => 1,
        ]);

        $response->assertOk()->assertJson([
            'error' => true,
        ]);
    }

    public function test_course_default_allows_customer_card_when_not_specified(): void
    {
        $course = Course::query()->create([
            'name' => 'Default Course',
            'price' => 40,
            'status' => CourseStatusEnum::PUBLISHED,
        ]);

        $this->assertTrue($course->accept_customer_card);
    }
}

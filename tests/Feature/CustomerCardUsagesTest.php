<?php

namespace Tests\Feature;

use Botble\ACL\Models\User;
use Botble\Courses\Enums\CourseStatusEnum;
use Botble\Courses\Models\Course;
use Botble\Courses\Models\CourseBooking;
use Botble\Hotel\Enums\BookingStatusEnum;
use Botble\Hotel\Models\Customer;
use Botble\Hotel\Models\CustomerCard;
use Botble\Hotel\Models\CustomerCardUsage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CustomerCardUsagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_usage_endpoint_renders_course_booking_details(): void
    {
        $this->withSession([]);

        $admin = User::query()->create([
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => 'secret',
            'first_name' => 'Admin',
            'last_name' => 'User',
        ]);

        $admin->forceFill(['super_user' => 1])->save();

        $customer = Customer::query()->create([
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane@example.com',
            'password' => 'secret',
        ]);

        $card = CustomerCard::query()->create([
            'name' => '10er Karte',
            'type' => 'custom',
            'base_price' => 50,
            'discount_percent' => 0,
            'units_total' => 10,
            'units_remaining' => 9,
            'valid_until' => now()->addMonth(),
            'is_active' => true,
            'created_by' => $admin->getKey(),
            'assigned_to' => $customer->getKey(),
        ]);

        $course = Course::query()->create([
            'name' => 'Yoga Basics',
            'price' => 50,
            'status' => CourseStatusEnum::PUBLISHED,
        ]);

        $booking = CourseBooking::query()->create([
            'course_id' => $course->getKey(),
            'customer_id' => $customer->getKey(),
            'transaction_id' => (string) Str::uuid(),
            'booking_number' => CourseBooking::generateUniqueBookingNumber(),
            'status' => BookingStatusEnum::COMPLETED,
        ]);

        CustomerCardUsage::query()->create([
            'card_id' => $card->getKey(),
            'course_booking_id' => $booking->getKey(),
            'course_id' => $course->getKey(),
            'units_used' => 1,
            'discount_amount' => 0,
        ]);

        $response = $this->actingAs($admin, 'web')->getJson(route('customer-cards.usages', $card));

        $response->assertOk();
        $response->assertJsonPath('error', false);

        $html = $response->json('data.html');

        $this->assertIsString($html);
        $this->assertStringContainsString('Yoga Basics', $html);
    }
}

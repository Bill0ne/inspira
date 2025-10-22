<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('course_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('ht_customers')->nullOnDelete();
            $table->string('transaction_id')->unique();
            $table->string('booking_number')->unique();
            $table->decimal('amount', 12, 2)->default(0);
            $table->decimal('sub_total', 12, 2)->default(0);
            $table->decimal('coupon_amount', 12, 2)->default(0);
            $table->string('coupon_code')->nullable();
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->string('status')->default('pending');
            $table->json('additional_info')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_booking');
    }
};

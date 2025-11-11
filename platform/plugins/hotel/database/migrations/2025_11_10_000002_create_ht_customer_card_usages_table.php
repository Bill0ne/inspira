<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ht_customer_card_usages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('card_id');
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('course_id');
            $table->unsignedInteger('units_used');
            $table->decimal('discount_amount', 15, 2);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('card_id')->references('id')->on('ht_customer_cards')->cascadeOnDelete();
            $table->foreign('booking_id')->references('id')->on('ht_bookings')->cascadeOnDelete();
            $table->foreign('course_id')->references('id')->on('ht_courses')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ht_customer_card_usages');
    }
};

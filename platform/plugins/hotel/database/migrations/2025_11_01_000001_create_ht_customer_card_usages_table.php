<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ht_customer_card_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('card_id')->constrained('ht_customer_cards')->cascadeOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained('ht_bookings')->nullOnDelete();
            $table->foreignId('course_id')->nullable()->constrained('ht_courses')->nullOnDelete();
            $table->unsignedInteger('units_used');
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ht_customer_card_usages');
    }
};

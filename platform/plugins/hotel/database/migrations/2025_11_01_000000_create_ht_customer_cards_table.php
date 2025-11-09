<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ht_customer_cards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type', 20);
            $table->unsignedInteger('units_total');
            $table->unsignedInteger('units_remaining');
            $table->decimal('base_price', 15, 2)->default(0);
            $table->unsignedTinyInteger('discount_percent')->default(0);
            $table->dateTime('valid_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ht_customer_cards');
    }
};

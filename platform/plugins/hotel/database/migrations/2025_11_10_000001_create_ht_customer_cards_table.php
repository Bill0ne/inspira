<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ht_customer_cards', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 191);
            $table->enum('type', ['5er', '10er', 'custom'])->default('custom');
            $table->decimal('base_price', 15, 2);
            $table->decimal('discount_percent', 5, 2);
            $table->unsignedInteger('units_total');
            $table->unsignedInteger('units_remaining');
            $table->dateTime('valid_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->timestamps();

            $table->unique(['name', 'assigned_to']);
            $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('assigned_to')->references('id')->on('ht_customers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ht_customer_cards');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('pc_quantity_discounts')) {
            Schema::create('pc_quantity_discounts', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->enum('condition_type', ['hours','bookings']);
                $table->integer('range_min')->nullable();
                $table->integer('range_max')->nullable();
                $table->enum('discount_type', ['absolute','percent']);
                $table->decimal('discount_value', 12, 2);
                $table->enum('apply_to', ['all','room'])->default('all');
                $table->integer('priority')->default(0);
                $table->enum('status', ['active','inactive'])->default('active');
                $table->timestamps();
            });
        }
    }
    public function down(): void {
        Schema::dropIfExists('pc_quantity_discounts');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('pc_price_tiers')) {
            Schema::create('pc_price_tiers', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->integer('priority')->default(0);
                $table->boolean('is_exclusive')->default(false);
                $table->enum('status', ['active','inactive'])->default('active');
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('ends_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('pc_price_rules')) {
            Schema::create('pc_price_rules', function (Blueprint $table) {
                $table->id();
                $table->foreignId('price_tier_id')->constrained('pc_price_tiers')->cascadeOnDelete();
                $table->string('customer_category');
                $table->enum('scope', ['all_rooms','room_categories'])->default('all_rooms');
                $table->json('room_category_ids')->nullable();
                $table->enum('calculation_type', ['percent','absolute']);
                $table->decimal('calculation_value', 12, 2);
                $table->enum('rounding_mode', ['none','up','down','nearest'])->default('none');
                $table->decimal('round_to', 12, 4)->default(0.01);
                $table->enum('status', ['active','inactive'])->default('active');
                $table->timestamps();

                $table->foreign('customer_category')->references('code')->on('pc_customer_categories')->cascadeOnUpdate();
            });
        }
    }
    public function down(): void {
        Schema::dropIfExists('pc_price_rules');
        Schema::dropIfExists('pc_price_tiers');
    }
};

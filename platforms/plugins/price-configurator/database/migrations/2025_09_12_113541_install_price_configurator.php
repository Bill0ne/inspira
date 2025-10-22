<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('pc_customer_categories')) {
            Schema::create('pc_customer_categories', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->string('label');
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('pc_price_tiers')) {
            Schema::create('pc_price_tiers', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->integer('priority')->default(100)->index();
                $table->enum('status', ['active', 'inactive'])->default('active')->index();
                $table->boolean('is_exclusive')->default(false);
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('ends_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('pc_price_rules')) {
            Schema::create('pc_price_rules', function (Blueprint $table) {
                $table->id();
                $table->foreignId('price_tier_id')->constrained('pc_price_tiers')->cascadeOnDelete();
                $table->string('customer_category')->index();
                $table->enum('scope', ['all_rooms', 'by_room_category'])->default('all_rooms')->index();
                $table->enum('calculation_type', ['percent', 'absolute'])->default('percent');
                $table->decimal('calculation_value', 12, 2)->default(0);
                $table->enum('rounding_mode', ['none', 'up', 'down', 'bankers'])->default('none');
                $table->decimal('round_to', 4, 2)->default(0.01);
                $table->enum('status', ['active', 'inactive'])->default('active')->index();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('pc_price_rule_room_category')) {
            Schema::create('pc_price_rule_room_category', function (Blueprint $table) {
                $table->unsignedBigInteger('price_rule_id');
                $table->unsignedBigInteger('room_category_id');
                $table->primary(['price_rule_id','room_category_id']);
            });
        }

        if (Schema::hasTable('ht_customers') && ! Schema::hasColumn('ht_customers', 'customer_category')) {
            Schema::table('ht_customers', function (Blueprint $table) {
                $table->string('customer_category')->default('STANDARD')->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('pc_price_rule_room_category')) Schema::drop('pc_price_rule_room_category');
        if (Schema::hasTable('pc_price_rules')) Schema::drop('pc_price_rules');
        if (Schema::hasTable('pc_price_tiers')) Schema::drop('pc_price_tiers');
        if (Schema::hasTable('pc_customer_categories')) Schema::drop('pc_customer_categories');
        if (Schema::hasTable('ht_customers') && Schema::hasColumn('ht_customers', 'customer_category')) {
            Schema::table('ht_customers', function (Blueprint $table) { $table->dropColumn('customer_category'); });
        }
    }
};

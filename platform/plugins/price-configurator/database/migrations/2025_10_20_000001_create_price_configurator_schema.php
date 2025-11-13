<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pconf_customer_categories')) {
            Schema::create('pconf_customer_categories', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->string('label');
                $table->text('description')->nullable();
                $table->string('status')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('pconf_tiers')) {
            Schema::create('pconf_tiers', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->integer('priority')->default(100)->index();
                $table->boolean('is_exclusive')->default(false);
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('ends_at')->nullable();
                $table->text('notes')->nullable();
                $table->string('status')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('pconf_rules')) {
            Schema::create('pconf_rules', function (Blueprint $table) {
                $table->id();
                $table->foreignId('price_tier_id')->constrained('pconf_tiers')->cascadeOnDelete();
                $table->foreignId('customer_category_id')
                    ->nullable()
                    ->constrained('pconf_customer_categories')
                    ->nullOnDelete();
                $table->string('scope')->nullable()->comment('e.g., all_products, by_category, specific_products');
                $table->json('target_ids')->nullable()->comment('Used when scope = by_category or specific_products');
                $table->string('target_type')->nullable()->comment('course, room, etc.');
                $table->string('calculation_type')->nullable()->comment('percent or absolute');
                $table->decimal('calculation_value', 12, 2)->default(0);
                $table->string('rounding_mode')->nullable()->comment('none, up, down, nearest');
                $table->decimal('round_to', 12, 4)->default(0.01);
                $table->string('adjustment_direction')->nullable();
                $table->string('status')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('pconf_rules', function (Blueprint $table) {
                if (! Schema::hasColumn('pconf_rules', 'target_type')) {
                    $table->string('target_type')->nullable()->after('scope');
                }

                if (! Schema::hasColumn('pconf_rules', 'adjustment_direction')) {
                    $table->string('adjustment_direction')->nullable()->after('round_to');
                }
            });
        }

        if (! Schema::hasTable('pconf_rule_category')) {
            Schema::create('pconf_rule_category', function (Blueprint $table) {
                $table->unsignedBigInteger('rule_id');
                $table->unsignedBigInteger('category_id');
                $table->primary(['rule_id', 'category_id']);
            });
        }

        if (! Schema::hasTable('pconf_quantity_discounts')) {
            Schema::create('pconf_quantity_discounts', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('condition_type')->nullable()->comment('quantity or amount');
                $table->integer('range_min')->nullable();
                $table->integer('range_max')->nullable();
                $table->string('discount_type')->nullable()->comment('absolute or percent');
                $table->decimal('discount_value', 12, 2);
                $table->integer('priority')->default(0);
                $table->string('status')->nullable();
                $table->timestamps();
            });
        }

        if (
            Schema::hasTable('ht_customers') &&
            ! Schema::hasColumn('ht_customers', 'customer_category_id') &&
            Schema::hasTable('pconf_customer_categories')
        ) {
            Schema::table('ht_customers', function (Blueprint $table) {
                $table->unsignedBigInteger('customer_category_id')->nullable()->after('id');
            });

            Schema::table('ht_customers', function (Blueprint $table) {
                $table->foreign('customer_category_id')
                    ->references('id')
                    ->on('pconf_customer_categories')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('ht_customers') && Schema::hasColumn('ht_customers', 'customer_category_id')) {
            Schema::table('ht_customers', function (Blueprint $table) {
                $table->dropForeign(['customer_category_id']);
                $table->dropColumn('customer_category_id');
            });
        }

        Schema::dropIfExists('pconf_quantity_discounts');
        Schema::dropIfExists('pconf_rule_category');
        Schema::dropIfExists('pconf_rules');
        Schema::dropIfExists('pconf_tiers');
        Schema::dropIfExists('pconf_customer_categories');
    }
};

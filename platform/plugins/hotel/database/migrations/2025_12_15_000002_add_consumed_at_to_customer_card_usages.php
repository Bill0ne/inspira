<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('ht_customer_card_usages')) {
            return;
        }

        Schema::table('ht_customer_card_usages', function (Blueprint $table) {
            if (! Schema::hasColumn('ht_customer_card_usages', 'discount_gross')) {
                $table->decimal('discount_gross', 12, 2)->default(0)->after('units_used');
            }

            if (! Schema::hasColumn('ht_customer_card_usages', 'coverage_type')) {
                $table->string('coverage_type', 20)->default('partial')->after('discount_gross');
            }

            if (! Schema::hasColumn('ht_customer_card_usages', 'status')) {
                $table->string('status', 20)->default('consumed')->after('coverage_type');
            }

            if (! Schema::hasColumn('ht_customer_card_usages', 'consumed_at')) {
                $table->timestamp('consumed_at')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('ht_customer_card_usages')) {
            return;
        }

        Schema::table('ht_customer_card_usages', function (Blueprint $table) {
            foreach (['consumed_at', 'status', 'coverage_type', 'discount_gross'] as $column) {
                if (Schema::hasColumn('ht_customer_card_usages', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('course_bookings')) {
            Schema::table('course_bookings', function (Blueprint $table) {
                if (! Schema::hasColumn('course_bookings', 'customer_card_coverage_type')) {
                    $table->string('customer_card_coverage_type', 20)
                        ->default('none')
                        ->after('customer_card_id');
                }

                if (! Schema::hasColumn('course_bookings', 'customer_card_discount_gross')) {
                    $table->decimal('customer_card_discount_gross', 12, 2)
                        ->default(0)
                        ->after('customer_card_coverage_type');
                }

                if (! Schema::hasColumn('course_bookings', 'payment_split_card_gross')) {
                    $table->decimal('payment_split_card_gross', 12, 2)
                        ->nullable()
                        ->after('customer_card_discount_gross');
                }

                if (! Schema::hasColumn('course_bookings', 'payment_split_online_gross')) {
                    $table->decimal('payment_split_online_gross', 12, 2)
                        ->nullable()
                        ->after('payment_split_card_gross');
                }
            });
        }

        if (Schema::hasTable('ht_customer_card_usages')) {
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
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('course_bookings')) {
            Schema::table('course_bookings', function (Blueprint $table) {
                foreach ([
                    'customer_card_coverage_type',
                    'customer_card_discount_gross',
                    'payment_split_card_gross',
                    'payment_split_online_gross',
                ] as $column) {
                    if (Schema::hasColumn('course_bookings', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::hasTable('ht_customer_card_usages')) {
            Schema::table('ht_customer_card_usages', function (Blueprint $table) {
                foreach (['discount_gross', 'coverage_type', 'status'] as $column) {
                    if (Schema::hasColumn('ht_customer_card_usages', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};

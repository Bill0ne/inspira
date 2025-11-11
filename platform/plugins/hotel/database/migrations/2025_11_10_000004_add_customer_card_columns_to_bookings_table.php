<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ht_bookings', function (Blueprint $table) {
            if (! Schema::hasColumn('ht_bookings', 'customer_card_id')) {
                $table->unsignedBigInteger('customer_card_id')->nullable()->after('coupon_code');
                $table->foreign('customer_card_id')->references('id')->on('ht_customer_cards')->nullOnDelete();
            }

            if (! Schema::hasColumn('ht_bookings', 'customer_card_discount')) {
                $table->decimal('customer_card_discount', 15, 2)->default(0)->after('customer_card_id');
            }

            if (! Schema::hasColumn('ht_bookings', 'customer_card_units_used')) {
                $table->unsignedInteger('customer_card_units_used')->default(0)->after('customer_card_discount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ht_bookings', function (Blueprint $table) {
            if (Schema::hasColumn('ht_bookings', 'customer_card_id')) {
                $table->dropForeign(['customer_card_id']);
                $table->dropColumn('customer_card_id');
            }

            if (Schema::hasColumn('ht_bookings', 'customer_card_discount')) {
                $table->dropColumn('customer_card_discount');
            }

            if (Schema::hasColumn('ht_bookings', 'customer_card_units_used')) {
                $table->dropColumn('customer_card_units_used');
            }
        });
    }
};

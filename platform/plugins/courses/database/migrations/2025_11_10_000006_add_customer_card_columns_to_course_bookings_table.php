<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('course_bookings')) {
            return;
        }

        Schema::table('course_bookings', function (Blueprint $table) {
            if (! Schema::hasColumn('course_bookings', 'customer_card_id')) {
                $table->unsignedBigInteger('customer_card_id')->nullable()->after('course_session_id');
                $table->foreign('customer_card_id')->references('id')->on('ht_customer_cards')->nullOnDelete();
            }

            if (! Schema::hasColumn('course_bookings', 'customer_card_discount')) {
                $table->decimal('customer_card_discount', 12, 2)->default(0)->after('customer_card_id');
            }

            if (! Schema::hasColumn('course_bookings', 'customer_card_units_used')) {
                $table->unsignedInteger('customer_card_units_used')->default(0)->after('customer_card_discount');
            }

            if (! Schema::hasColumn('course_bookings', 'customer_card_consumed_at')) {
                $table->timestamp('customer_card_consumed_at')->nullable()->after('customer_card_units_used');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('course_bookings')) {
            return;
        }

        Schema::table('course_bookings', function (Blueprint $table) {
            if (Schema::hasColumn('course_bookings', 'customer_card_id')) {
                $table->dropForeign(['customer_card_id']);
                $table->dropColumn('customer_card_id');
            }

            if (Schema::hasColumn('course_bookings', 'customer_card_discount')) {
                $table->dropColumn('customer_card_discount');
            }

            if (Schema::hasColumn('course_bookings', 'customer_card_units_used')) {
                $table->dropColumn('customer_card_units_used');
            }

            if (Schema::hasColumn('course_bookings', 'customer_card_consumed_at')) {
                $table->dropColumn('customer_card_consumed_at');
            }
        });
    }
};

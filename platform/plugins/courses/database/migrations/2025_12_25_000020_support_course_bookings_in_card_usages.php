<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Throwable;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('ht_customer_card_usages')) {
            return;
        }

        Schema::table('ht_customer_card_usages', function (Blueprint $table) {
            if (Schema::hasColumn('ht_customer_card_usages', 'booking_id')) {
                Schema::disableForeignKeyConstraints();

                try {
                    $table->dropForeign(['booking_id']);
                } catch (Throwable) {
                }

                $table->unsignedBigInteger('booking_id')->nullable()->change();

                Schema::enableForeignKeyConstraints();
            }

            if (! Schema::hasColumn('ht_customer_card_usages', 'course_booking_id')) {
                $table->unsignedBigInteger('course_booking_id')->nullable()->after('booking_id');
            }
        });

        Schema::table('ht_customer_card_usages', function (Blueprint $table) {
            if (
                Schema::hasColumn('ht_customer_card_usages', 'booking_id')
                && Schema::hasTable('ht_bookings')
            ) {
                try {
                    $table->foreign('booking_id')->references('id')->on('ht_bookings')->cascadeOnDelete();
                } catch (Throwable) {
                }
            }

            if (
                Schema::hasColumn('ht_customer_card_usages', 'course_booking_id')
                && Schema::hasTable('course_bookings')
            ) {
                try {
                    $table->foreign('course_booking_id')
                        ->references('id')
                        ->on('course_bookings')
                        ->cascadeOnDelete();
                } catch (Throwable) {
                }
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('ht_customer_card_usages')) {
            return;
        }

        Schema::table('ht_customer_card_usages', function (Blueprint $table) {
            if (Schema::hasColumn('ht_customer_card_usages', 'course_booking_id')) {
                try {
                    $table->dropForeign(['course_booking_id']);
                } catch (Throwable) {
                }

                $table->dropColumn('course_booking_id');
            }

            if (Schema::hasColumn('ht_customer_card_usages', 'booking_id')) {
                try {
                    $table->dropForeign(['booking_id']);
                } catch (Throwable) {
                }

                try {
                    $table->unsignedBigInteger('booking_id')->nullable(false)->change();
                } catch (Throwable) {
                }

                if (Schema::hasTable('ht_bookings')) {
                    try {
                        $table->foreign('booking_id')->references('id')->on('ht_bookings')->cascadeOnDelete();
                    } catch (Throwable) {
                    }
                }
            }
        });
    }
};

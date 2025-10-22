<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('ht_booking_rooms')) {
            Schema::table('ht_booking_rooms', function (Blueprint $table) {
                if (!Schema::hasColumn('ht_booking_rooms', 'start_at')) {
                    $table->dateTime('start_at')->nullable()->after('start_date');
                }
                if (!Schema::hasColumn('ht_booking_rooms', 'end_at')) {
                    $table->dateTime('end_at')->nullable()->after('end_date');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('ht_booking_rooms')) {
            Schema::table('ht_booking_rooms', function (Blueprint $table) {
                if (Schema::hasColumn('ht_booking_rooms', 'start_at')) {
                    $table->dropColumn('start_at');
                }
                if (Schema::hasColumn('ht_booking_rooms', 'end_at')) {
                    $table->dropColumn('end_at');
                }
            });
        }
    }
};

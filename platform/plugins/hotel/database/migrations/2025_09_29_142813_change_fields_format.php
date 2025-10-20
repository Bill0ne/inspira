<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Update booking rooms table
        Schema::table('ht_booking_rooms', function (Blueprint $table) {
            $table->dateTime('start_date')->change();
            $table->dateTime('end_date')->change();
        });

        // Update room dates table
        Schema::table('ht_room_dates', function (Blueprint $table) {
            $table->dateTime('start_date')->change();
            $table->dateTime('end_date')->change();
        });
    }

    public function down(): void
    {
        // Revert booking rooms table
        Schema::table('ht_booking_rooms', function (Blueprint $table) {
            $table->date('start_date')->change();
            $table->date('end_date')->change();
        });

        // Revert room dates table
        Schema::table('ht_room_dates', function (Blueprint $table) {
            $table->date('start_date')->change();
            $table->date('end_date')->change();
        });
    }
};

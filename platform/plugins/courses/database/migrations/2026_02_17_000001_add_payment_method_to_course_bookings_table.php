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
            if (! Schema::hasColumn('course_bookings', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('payment_id');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('course_bookings')) {
            return;
        }

        Schema::table('course_bookings', function (Blueprint $table) {
            if (Schema::hasColumn('course_bookings', 'payment_method')) {
                $table->dropColumn('payment_method');
            }
        });
    }
};

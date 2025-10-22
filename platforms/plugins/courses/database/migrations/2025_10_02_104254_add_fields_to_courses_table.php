<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->boolean('is_recurring')->default(false);
            $table->string('recurring_type')->nullable(); // daily, weekly, monthly
            $table->integer('recurring_interval')->nullable()->default(1); // every X weeks/days
            $table->dateTime('recurring_until')->nullable(); // end date of recurrence
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            //
        });
    }
};

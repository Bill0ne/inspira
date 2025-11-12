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
        if (! Schema::hasColumn('course_sessions', 'is_manual')) {
            Schema::table('course_sessions', function (Blueprint $table) {
                $table->boolean('is_manual')->default(false)->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('course_sessions', 'is_manual')) {
            Schema::table('course_sessions', function (Blueprint $table) {
                $table->dropColumn('is_manual');
            });
        }
    }
};

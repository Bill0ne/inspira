<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('courses', 'accept_customer_card')) {
            return;
        }

        DB::table('courses')
            ->whereNull('accept_customer_card')
            ->update(['accept_customer_card' => true]);

        Schema::table('courses', function (Blueprint $table) {
            $table->boolean('accept_customer_card')->default(true)->change();
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('courses', 'accept_customer_card')) {
            return;
        }

        Schema::table('courses', function (Blueprint $table) {
            $table->boolean('accept_customer_card')->default(false)->change();
        });
    }
};

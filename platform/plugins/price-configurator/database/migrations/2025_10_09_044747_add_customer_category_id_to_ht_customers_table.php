<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
//        Schema::table('ht_customers', function (Blueprint $table) {
//            if (!Schema::hasColumn('ht_customers', 'customer_category_id')) {
//                $table->unsignedBigInteger('customer_category_id')
//                    ->nullable()
//                    ->after('id');
//
//                $table->foreign('customer_category_id')
//                    ->references('id')
//                    ->on('pconf_customer_categories')
//                    ->onDelete('set null');
//            }
//        });
    }

    public function down(): void
    {
        Schema::table('ht_customers', function (Blueprint $table) {
            if (Schema::hasColumn('ht_customers', 'customer_category_id')) {
                $table->dropForeign(['customer_category_id']);
                $table->dropColumn('customer_category_id');
            }
        });
    }
};


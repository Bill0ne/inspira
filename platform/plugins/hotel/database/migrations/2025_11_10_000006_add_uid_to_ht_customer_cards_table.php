<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ht_customer_cards')) {
            return;
        }

        Schema::table('ht_customer_cards', function (Blueprint $table) {
            if (! Schema::hasColumn('ht_customer_cards', 'uid')) {
                $table->string('uid', 40)->nullable()->unique()->after('name');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('ht_customer_cards')) {
            return;
        }

        Schema::table('ht_customer_cards', function (Blueprint $table) {
            if (Schema::hasColumn('ht_customer_cards', 'uid')) {
                $table->dropUnique('ht_customer_cards_uid_unique');
                $table->dropColumn('uid');
            }
        });
    }
};

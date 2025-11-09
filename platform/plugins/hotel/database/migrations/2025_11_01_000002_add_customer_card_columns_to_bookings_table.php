<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ht_bookings', function (Blueprint $table) {
            $table->foreignId('customer_card_id')->nullable()->after('coupon_code')->constrained('ht_customer_cards')->nullOnDelete();
            $table->decimal('customer_card_discount', 15, 2)->default(0)->after('customer_card_id');
            $table->unsignedInteger('customer_card_units_used')->default(0)->after('customer_card_discount');
        });
    }

    public function down(): void
    {
        Schema::table('ht_bookings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('customer_card_id');
            $table->dropColumn(['customer_card_discount', 'customer_card_units_used']);
        });
    }
};

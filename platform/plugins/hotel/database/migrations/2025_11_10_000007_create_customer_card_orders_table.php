<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('customer_card_orders')) {
            return;
        }

        Schema::create('customer_card_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('ht_customers')->cascadeOnDelete();
            $table->foreignId('card_template_id')->constrained('ht_customer_cards')->cascadeOnDelete();
            $table->foreignId('assigned_card_id')->nullable()->constrained('ht_customer_cards')->nullOnDelete();
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('status')->default('pending');
            $table->string('transaction_id')->unique();
            $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_card_orders');
    }
};

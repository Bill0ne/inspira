<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('insp_cancellation_rules', function (Blueprint $table): void {
            $table->id();
            $table->string('type', 20);
            $table->integer('from_days')->nullable();
            $table->integer('to_days')->nullable();
            $table->integer('refund_percent')->default(0);
            $table->text('description')->nullable();
            $table->boolean('active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('insp_cancellations', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->string('booking_type', 20);
            $table->string('booking_reference')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('rule_id')->nullable();
            $table->decimal('refund_amount', 12, 2)->default(0);
            $table->integer('refund_percent')->default(0);
            $table->string('status', 50)->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['booking_id', 'booking_type']);
            $table->foreign('customer_id')->references('id')->on('ht_customers')->nullOnDelete();
            $table->foreign('rule_id')->references('id')->on('insp_cancellation_rules')->nullOnDelete();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('insp_cancellations');
        Schema::dropIfExists('insp_cancellation_rules');
    }
};

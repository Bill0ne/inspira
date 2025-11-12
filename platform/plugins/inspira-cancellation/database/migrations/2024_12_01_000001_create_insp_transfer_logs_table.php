<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('insp_transfer_logs')) {
            return;
        }

        Schema::create('insp_transfer_logs', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->string('booking_type', 20);
            $table->unsignedBigInteger('old_customer_id')->nullable();
            $table->unsignedBigInteger('new_customer_id')->nullable();
            $table->timestamps();

            $table->index(['booking_id', 'booking_type']);
            $table->foreign('old_customer_id')->references('id')->on('ht_customers')->nullOnDelete();
            $table->foreign('new_customer_id')->references('id')->on('ht_customers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insp_transfer_logs');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('pc_customer_categories')) {
            Schema::create('pc_customer_categories', function (Blueprint $table) {
                $table->string('code')->primary();
                $table->string('label');
                $table->enum('status', ['active','inactive'])->default('active');
                $table->timestamps();
            });
        }
    }
    public function down(): void {
        Schema::dropIfExists('pc_customer_categories');
    }
};

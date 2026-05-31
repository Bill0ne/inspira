<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('community_members')) {
            return;
        }

        Schema::create('community_members', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('ht_customers')->nullOnDelete();
            $table->string('name', 255);
            $table->string('photo', 255)->nullable();
            $table->text('quote')->nullable();
            $table->string('short_description', 400)->nullable();
            $table->text('description')->nullable();
            $table->string('status', 60)->default('published');
            $table->timestamps();

            $table->unique('customer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('community_members');
    }
};

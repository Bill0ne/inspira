<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('personal_dashboard_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('key')->unique();
            $table->json('value')->nullable();
            $table->timestamps();
        });

        Schema::create('personal_dashboard_custom_widgets', function (Blueprint $table): void {
            $table->id();
            $table->string('key')->unique();
            $table->json('title');
            $table->json('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->string('column_class')->nullable();
            $table->string('view_path')->nullable();
            $table->string('handler')->nullable();
            $table->string('ajax_route')->nullable();
            $table->boolean('has_load_callback')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->json('settings')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personal_dashboard_custom_widgets');
        Schema::dropIfExists('personal_dashboard_settings');
    }
};

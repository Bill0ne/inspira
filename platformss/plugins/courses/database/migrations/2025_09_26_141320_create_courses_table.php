<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('instructors')) {
            Schema::create('instructors', function (Blueprint $table) {
                $table->id();
                $table->string('name', 255);
                $table->string('photo', 255)->nullable();
                $table->string('email', 120)->nullable();
                $table->string('phone', 60)->nullable();
                $table->text('bio')->nullable();
                $table->string('status', 60)->default('published');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('course_categories')) {
            Schema::create('course_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name', 255);
                $table->text('description')->nullable();
                $table->string('status', 60)->default('published');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('courses')) {
            Schema::create('courses', function (Blueprint $table) {
                $table->id();
                $table->string('name', 255);
                $table->text('description')->nullable();
                $table->decimal('price', 15, 2)->default(0);
                $table->string('duration', 120)->nullable();
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->foreignId('instructor_id')->nullable()->constrained('instructors')->nullOnDelete();
                $table->foreignId('category_id')->nullable()->constrained('course_categories')->nullOnDelete();
                $table->string('status', 60)->default('published');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
        Schema::dropIfExists('course_categories');
        Schema::dropIfExists('instructors');
    }
};

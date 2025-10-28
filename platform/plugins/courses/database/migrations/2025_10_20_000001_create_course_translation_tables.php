<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('courses_translations')) {
            Schema::create('courses_translations', function (Blueprint $table): void {
                $table->string('lang_code', 20);
                $table->unsignedBigInteger('courses_id');
                $table->string('name');
                $table->text('description')->nullable();

                $table->primary(['lang_code', 'courses_id'], 'courses_translations_primary');
            });
        }

        if (! Schema::hasTable('course_categories_translations')) {
            Schema::create('course_categories_translations', function (Blueprint $table): void {
                $table->string('lang_code', 20);
                $table->unsignedBigInteger('course_categories_id');
                $table->string('name');
                $table->text('description')->nullable();

                $table->primary(['lang_code', 'course_categories_id'], 'course_categories_translations_primary');
            });
        }

        if (! Schema::hasTable('instructors_translations')) {
            Schema::create('instructors_translations', function (Blueprint $table): void {
                $table->string('lang_code', 20);
                $table->unsignedBigInteger('instructors_id');
                $table->string('name');
                $table->text('bio')->nullable();

                $table->primary(['lang_code', 'instructors_id'], 'instructors_translations_primary');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('instructors_translations');
        Schema::dropIfExists('course_categories_translations');
        Schema::dropIfExists('courses_translations');
    }
};

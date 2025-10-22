<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('insp_courses', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('slug')->unique();
            $t->text('description')->nullable();
            $t->timestamps();
        });

        Schema::create('insp_course_sessions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('course_id')->constrained('insp_courses')->cascadeOnDelete();
            $t->dateTime('starts_at');
            $t->dateTime('ends_at')->nullable();
            $t->string('location')->nullable();
            $t->unsignedInteger('capacity')->default(0);
            $t->unsignedInteger('seats_sold')->default(0);
            $t->decimal('price', 15, 2)->default(0);
            $t->timestamps();
        });

        Schema::create('insp_course_purchases', function (Blueprint $t) {
            $t->id();
            $t->foreignId('session_id')->constrained('insp_course_sessions')->cascadeOnDelete();
            $t->unsignedBigInteger('customer_id')->nullable();
            $t->unsignedInteger('qty')->default(1);
            $t->decimal('amount', 15, 2);
            $t->string('currency', 5)->default('EUR');
            $t->string('transaction_id')->unique();
            $t->unsignedBigInteger('payment_id')->nullable();
            $t->string('status')->default('pending');
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insp_course_purchases');
        Schema::dropIfExists('insp_course_sessions');
        Schema::dropIfExists('insp_courses');
    }
};

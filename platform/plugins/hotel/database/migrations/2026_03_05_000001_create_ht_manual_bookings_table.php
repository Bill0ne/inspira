<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('ht_manual_bookings', function (Blueprint $table): void {
            $table->id();
            $table->string('type');
            $table->foreignId('room_id')->nullable()->constrained('ht_rooms')->nullOnDelete();
            $table->foreignId('course_id')->nullable()->constrained('courses')->nullOnDelete();
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->index(['type', 'start_at', 'end_at']);
            $table->check("type in ('room', 'course')");
            $table->check('end_at > start_at');
            $table->check("(type = 'room' and room_id is not null and course_id is null) or (type = 'course' and course_id is not null and room_id is null)");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ht_manual_bookings');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('ht_manual_bookings')) {
            return;
        }

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
        });

        // CHECK-Constraints werden hier als raw SQL angelegt, weil Botbles
        // eigene Blueprint-Subklasse das fluente $table->check() nicht
        // unterstützt. MySQL < 8.0.16 ignoriert CHECK still; die App-Layer
        // (ManualBookingRequest) erzwingt die Regeln ohnehin.
        if (DB::getDriverName() === 'mysql') {
            try {
                DB::statement("ALTER TABLE ht_manual_bookings ADD CONSTRAINT ht_manual_bookings_type_check CHECK (type in ('room', 'course'))");
                DB::statement('ALTER TABLE ht_manual_bookings ADD CONSTRAINT ht_manual_bookings_dates_check CHECK (end_at > start_at)');
                DB::statement("ALTER TABLE ht_manual_bookings ADD CONSTRAINT ht_manual_bookings_target_check CHECK ((type = 'room' AND room_id IS NOT NULL AND course_id IS NULL) OR (type = 'course' AND course_id IS NOT NULL AND room_id IS NULL))");
            } catch (\Throwable) {
                // Älteres MySQL (< 8.0.16) lehnt CHECK-Constraints ab – kein Problem,
                // die Validierung passiert im Application-Layer.
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ht_manual_bookings');
    }
};

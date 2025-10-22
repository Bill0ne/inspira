<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('insp_instructors', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('email')->nullable();
            $t->string('phone')->nullable();
            $t->text('bio')->nullable();
            $t->timestamps();
        });

        if (!Schema::hasColumn('insp_courses', 'instructor_id')) {
            Schema::table('insp_courses', function (Blueprint $t) {
                $t->unsignedBigInteger('instructor_id')->nullable()->after('description');
                $t->foreign('instructor_id')->references('id')->on('insp_instructors')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('insp_courses', 'instructor_id')) {
            Schema::table('insp_courses', function (Blueprint $t) {
                $t->dropForeign(['instructor_id']);
                $t->dropColumn('instructor_id');
            });
        }
        Schema::dropIfExists('insp_instructors');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('insp_cancellations', function (Blueprint $table): void {
            if (! Schema::hasColumn('insp_cancellations', 'total_amount')) {
                $table->decimal('total_amount', 12, 2)->default(0)->after('refund_amount');
            }

            if (! Schema::hasColumn('insp_cancellations', 'fee_amount')) {
                $table->decimal('fee_amount', 12, 2)->default(0)->after('total_amount');
            }

            if (! Schema::hasColumn('insp_cancellations', 'days_until_start')) {
                $table->integer('days_until_start')->nullable()->after('fee_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('insp_cancellations', function (Blueprint $table): void {
            foreach (['days_until_start', 'fee_amount', 'total_amount'] as $column) {
                if (Schema::hasColumn('insp_cancellations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

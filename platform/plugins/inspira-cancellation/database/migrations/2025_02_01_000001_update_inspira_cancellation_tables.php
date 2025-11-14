<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('insp_cancellations', function (Blueprint $table): void {
            if (! Schema::hasColumn('insp_cancellations', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('status');
            }

            if (! Schema::hasColumn('insp_cancellations', 'approved_by')) {
                $table
                    ->foreignId('approved_by')
                    ->nullable()
                    ->after('approved_at')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('insp_cancellations', 'refunded_at')) {
                $table->timestamp('refunded_at')->nullable()->after('approved_by');
            }

            if (! Schema::hasColumn('insp_cancellations', 'refunded_by')) {
                $table
                    ->foreignId('refunded_by')
                    ->nullable()
                    ->after('refunded_at')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });

        Schema::table('insp_transfer_logs', function (Blueprint $table): void {
            if (! Schema::hasColumn('insp_transfer_logs', 'status')) {
                $table->string('status', 50)->default('pending')->after('new_customer_id');
            }

            if (! Schema::hasColumn('insp_transfer_logs', 'payload')) {
                $table->json('payload')->nullable()->after('status');
            }

            if (! Schema::hasColumn('insp_transfer_logs', 'requested_by')) {
                $table
                    ->foreignId('requested_by')
                    ->nullable()
                    ->after('payload')
                    ->constrained('ht_customers')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('insp_transfer_logs', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('requested_by');
            }

            if (! Schema::hasColumn('insp_transfer_logs', 'approved_by')) {
                $table
                    ->foreignId('approved_by')
                    ->nullable()
                    ->after('approved_at')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('insp_cancellations', function (Blueprint $table): void {
            if (Schema::hasColumn('insp_cancellations', 'approved_by')) {
                $table->dropConstrainedForeignId('approved_by');
            }

            if (Schema::hasColumn('insp_cancellations', 'refunded_by')) {
                $table->dropConstrainedForeignId('refunded_by');
            }

            foreach (['approved_at', 'refunded_at'] as $column) {
                if (Schema::hasColumn('insp_cancellations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('insp_transfer_logs', function (Blueprint $table): void {
            if (Schema::hasColumn('insp_transfer_logs', 'approved_by')) {
                $table->dropConstrainedForeignId('approved_by');
            }

            if (Schema::hasColumn('insp_transfer_logs', 'requested_by')) {
                $table->dropConstrainedForeignId('requested_by');
            }

            foreach (['status', 'payload', 'approved_at'] as $column) {
                if (Schema::hasColumn('insp_transfer_logs', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    protected function dropForeignIfExists(string $referencedTable): void
    {
        if (! Schema::hasTable('ht_customer_cards') || ! Schema::hasColumn('ht_customer_cards', 'assigned_to')) {
            return;
        }

        $connection = Schema::getConnection();
        $tableName = $connection->getTablePrefix() . 'ht_customer_cards';

        $constraintName = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', $connection->getDatabaseName())
            ->where('TABLE_NAME', $tableName)
            ->where('COLUMN_NAME', 'assigned_to')
            ->where('REFERENCED_TABLE_NAME', $referencedTable)
            ->value('CONSTRAINT_NAME');

        if (! $constraintName) {
            return;
        }

        Schema::table('ht_customer_cards', function (Blueprint $table) use ($constraintName) {
            $table->dropForeign($constraintName);
        });
    }

    public function up(): void
    {
        if (! Schema::hasTable('ht_customer_cards') || ! Schema::hasColumn('ht_customer_cards', 'assigned_to')) {
            return;
        }

        $this->dropForeignIfExists('users');

        Schema::table('ht_customer_cards', function (Blueprint $table) {
            $table->unsignedBigInteger('assigned_to')->nullable()->change();
            $table->foreign('assigned_to')->references('id')->on('ht_customers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('ht_customer_cards') || ! Schema::hasColumn('ht_customer_cards', 'assigned_to')) {
            return;
        }

        $this->dropForeignIfExists('ht_customers');

        Schema::table('ht_customer_cards', function (Blueprint $table) {
            $table->unsignedBigInteger('assigned_to')->nullable()->change();
            $table->foreign('assigned_to')->references('id')->on('users')->nullOnDelete();
        });
    }
};

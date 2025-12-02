<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected function uniqueIndexExists(string $indexName): bool
    {
        $connection = Schema::getConnection();
        $tableName = $connection->getTablePrefix() . 'ht_customer_cards';

        return DB::table('information_schema.statistics')
            ->where('TABLE_SCHEMA', $connection->getDatabaseName())
            ->where('TABLE_NAME', $tableName)
            ->where('INDEX_NAME', $indexName)
            ->exists();
    }

    public function up(): void
    {
        if (! Schema::hasTable('ht_customer_cards')) {
            return;
        }

        $indexName = 'ht_customer_cards_name_assigned_to_unique';

        if (! $this->uniqueIndexExists($indexName)) {
            return;
        }

        Schema::table('ht_customer_cards', function (Blueprint $table) use ($indexName) {
            $table->dropUnique($indexName);
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('ht_customer_cards')) {
            return;
        }

        $indexName = 'ht_customer_cards_name_assigned_to_unique';

        if ($this->uniqueIndexExists($indexName)) {
            return;
        }

        Schema::table('ht_customer_cards', function (Blueprint $table) {
            if (Schema::hasColumn('ht_customer_cards', 'name') && Schema::hasColumn('ht_customer_cards', 'assigned_to')) {
                $table->unique(['name', 'assigned_to']);
            }
        });
    }
};

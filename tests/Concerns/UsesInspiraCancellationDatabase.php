<?php

namespace Tests\Concerns;

use Illuminate\Support\Facades\Artisan;

trait UsesInspiraCancellationDatabase
{
    protected function runInspiraCancellationMigrations(): void
    {
        Artisan::call('migrate', [
            '--database' => config('database.default'),
            '--path' => 'platform/plugins/inspira-cancellation/database/migrations',
            '--force' => true,
        ]);
    }
}

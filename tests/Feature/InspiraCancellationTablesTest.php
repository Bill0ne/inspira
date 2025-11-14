<?php

namespace Tests\Feature;

use Botble\ACL\Models\User;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Supports\Core;
use Botble\InspiraCancellation\Tables\CancellationRuleTable;
use Botble\InspiraCancellation\Tables\CancellationTable;
use Botble\InspiraCancellation\Tables\TransferLogTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class InspiraCancellationTablesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['auth.providers.users.model' => User::class]);

        Core::make()->skipLicenseReminder();
    }

    public function test_admin_tables_return_json_data(): void
    {
        $user = User::query()->create([
            'email' => 'admin@example.com',
            'username' => 'admin',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'password' => Hash::make('password'),
            'super_user' => true,
        ]);

        $this->actingAs($user);

        $adminPrefix = BaseHelper::getAdminPrefix();

        $tables = [
            'inspira-cancellation/cancellations' => Str::slug(Str::snake(CancellationTable::class)),
            'inspira-cancellation/rules' => Str::slug(Str::snake(CancellationRuleTable::class)),
            'inspira-cancellation/transfers' => Str::slug(Str::snake(TransferLogTable::class)),
        ];

        foreach ($tables as $path => $tableId) {
            $response = $this->getJson(sprintf('/%s/%s?table=%s', $adminPrefix, $path, $tableId), [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

            $response->assertOk()->assertJsonStructure(['data']);
        }
    }
}

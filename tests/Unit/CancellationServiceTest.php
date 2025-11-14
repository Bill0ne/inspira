<?php

namespace Tests\Unit;

use Botble\InspiraCancellation\Models\CancellationRule;
use Botble\InspiraCancellation\Services\CancellationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\Concerns\UsesInspiraCancellationDatabase;
use Tests\TestCase;

class CancellationServiceTest extends TestCase
{
    use RefreshDatabase;
    use UsesInspiraCancellationDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->runInspiraCancellationMigrations();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_it_selects_rules_on_interval_boundaries(): void
    {
        Carbon::setTestNow(Carbon::create(2025, 1, 1));

        $ruleA = CancellationRule::query()->create([
            'type' => 'course',
            'from_days' => null,
            'to_days' => 5,
            'refund_percent' => 80,
            'active' => true,
            'order' => 1,
        ]);

        $ruleB = CancellationRule::query()->create([
            'type' => 'course',
            'from_days' => 6,
            'to_days' => 10,
            'refund_percent' => 60,
            'active' => true,
            'order' => 2,
        ]);

        $ruleC = CancellationRule::query()->create([
            'type' => 'course',
            'from_days' => 11,
            'to_days' => null,
            'refund_percent' => 30,
            'active' => true,
            'order' => 3,
        ]);

        $service = new CancellationService();

        $this->assertTrue($service->findRuleFor('course', Carbon::now()->addDays(5))->is($ruleA));
        $this->assertTrue($service->findRuleFor('course', Carbon::now()->addDays(6))->is($ruleB));
        $this->assertTrue($service->findRuleFor('course', Carbon::now()->addDays(25))->is($ruleC));
    }

    public function test_it_logs_when_multiple_rules_match(): void
    {
        Carbon::setTestNow(Carbon::create(2025, 1, 1));

        CancellationRule::query()->create([
            'type' => 'course',
            'from_days' => 0,
            'to_days' => 10,
            'refund_percent' => 50,
            'active' => true,
            'order' => 1,
        ]);

        CancellationRule::query()->create([
            'type' => 'course',
            'from_days' => 5,
            'to_days' => 15,
            'refund_percent' => 40,
            'active' => true,
            'order' => 2,
        ]);

        Log::spy();

        $service = new CancellationService();
        $service->findRuleFor('course', Carbon::now()->addDays(6));

        Log::shouldHaveReceived('warning')->once();
    }
}

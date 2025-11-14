<?php

namespace Tests\Unit;

use Botble\InspiraCancellation\Http\Requests\CancellationRuleRequest;
use Botble\InspiraCancellation\Models\CancellationRule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Route;
use Illuminate\Validation\Validator;
use Tests\Concerns\UsesInspiraCancellationDatabase;
use Tests\TestCase;

class CancellationRuleRequestTest extends TestCase
{
    use RefreshDatabase;
    use UsesInspiraCancellationDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->runInspiraCancellationMigrations();
    }

    public function test_it_accepts_valid_interval(): void
    {
        $validator = $this->makeValidator([
            'type' => 'course',
            'from_days' => '0',
            'to_days' => '5',
            'refund_percent' => 50,
            'active' => true,
        ]);

        $this->assertFalse($validator->fails());
    }

    public function test_it_rejects_invalid_range(): void
    {
        $validator = $this->makeValidator([
            'type' => 'room',
            'from_days' => '10',
            'to_days' => '5',
            'refund_percent' => 80,
            'active' => true,
        ]);

        $this->assertTrue($validator->fails());
        $this->assertStringContainsString(
            'less than or equal',
            $validator->errors()->first('type')
        );
    }

    public function test_it_rejects_overlapping_active_rule(): void
    {
        CancellationRule::query()->create([
            'type' => 'course',
            'from_days' => 0,
            'to_days' => 10,
            'refund_percent' => 50,
            'active' => true,
            'order' => 0,
        ]);

        $validator = $this->makeValidator([
            'type' => 'course',
            'from_days' => '5',
            'to_days' => '12',
            'refund_percent' => 40,
            'active' => true,
        ]);

        $this->assertTrue($validator->fails());
        $this->assertStringContainsString(
            'overlaps',
            $validator->errors()->first('type')
        );
    }

    public function test_it_allows_overlapping_when_rule_is_inactive(): void
    {
        CancellationRule::query()->create([
            'type' => 'course',
            'from_days' => 0,
            'to_days' => 10,
            'refund_percent' => 50,
            'active' => true,
            'order' => 0,
        ]);

        $validator = $this->makeValidator([
            'type' => 'course',
            'from_days' => '5',
            'to_days' => '12',
            'refund_percent' => 40,
            'active' => false,
        ]);

        $this->assertFalse($validator->fails());
    }

    protected function makeValidator(array $data): Validator
    {
        $request = new CancellationRuleRequest();
        $request->setContainer(app());
        $request->setRedirector(app('redirect'));
        $request->setRouteResolver(fn () => new Route('POST', '/dummy', []));
        $request->initialize([], $data, [], [], [], ['REQUEST_METHOD' => 'POST']);

        return $request->getValidatorInstance();
    }
}

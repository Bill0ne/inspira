<?php

namespace Tests\Unit;

use Botble\PriceConfigurator\Enums\TargetTypeEnum;
use Botble\PriceConfigurator\Services\PriceConfiguratorService;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class PriceConfiguratorServiceTest extends TestCase
{
    public function test_quantity_discount_is_applied_without_rules(): void
    {
        $service = new class extends PriceConfiguratorService {
            public int $quantityDiscountCalls = 0;

            protected function getApplicableRules(TargetTypeEnum|string $targetType, int $targetId, ?\Botble\Hotel\Models\Customer $customer): Collection
            {
                return collect();
            }

            protected function applyQuantityDiscount(float $price, int $hours): float
            {
                $this->quantityDiscountCalls++;

                return $price - 10;
            }
        };

        $finalPrice = $service->calculatePrice(100, TargetTypeEnum::ROOM, 1, null, 4);

        $this->assertSame(90.0, $finalPrice);
        $this->assertSame(1, $service->quantityDiscountCalls);
    }
}

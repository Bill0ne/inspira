<?php

namespace Botble\PriceConfigurator\Services;

use Botble\Hotel\Models\Customer;
use Botble\PriceConfigurator\Enums\{
    PriceConfiguratorStatusEnum,
    ScopeEnum,
    TargetTypeEnum,
    CalculationTypeEnum,
    RoundingModeEnum,
    RuleDirectionEnum,
    ConditionTypeEnum
};
use Botble\PriceConfigurator\Models\{
    Rule,
    Tier,
    QuantityDiscount
};
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PriceConfiguratorService
{
    public function calculatePrice(
        float $basePrice,
        TargetTypeEnum|string $targetType,
        int $targetId,
        ?Customer $customer = null,
        int $quantity = 1
    ): float {
        $details = $this->calculatePriceWithDetails(
            $basePrice,
            $targetType,
            $targetId,
            $customer,
            $quantity
        );

        return $details['final_price'];
    }

    public function calculatePriceWithDetails(
        float $basePrice,
        TargetTypeEnum|string $targetType,
        int $targetId,
        ?Customer $customer = null,
        int $quantity = 1
    ): array {
        $rules = $this->getApplicableRules($targetType, $targetId, $customer);

        $priceAfterRules = $basePrice;

        if (! $rules->isEmpty()) {
            foreach ($rules as $rule) {
                $priceAfterRules = $this->applyRule($priceAfterRules, $rule);
            }
        }

        $quantityDiscountAmount = 0;
        $finalPrice = $priceAfterRules;

        if ($targetType == TargetTypeEnum::ROOM) {
            $discountedPrice = $this->applyQuantityDiscount($priceAfterRules, $quantity);
            $quantityDiscountAmount = max($priceAfterRules - $discountedPrice, 0);
            $finalPrice = $discountedPrice;
        }

        $finalPrice = max($finalPrice, 0);

        return [
            'original_price' => $basePrice,
            'price_after_rules' => $priceAfterRules,
            'final_price' => $finalPrice,
            'rules_discount_amount' => max($basePrice - $priceAfterRules, 0),
            'quantity_discount_amount' => $quantityDiscountAmount,
        ];
    }

    protected function getApplicableRules(TargetTypeEnum|string $targetType, int $targetId, ?Customer $customer): Collection
    {
        $now = Carbon::now();

        $tiers = Tier::query()
            ->where('status', PriceConfiguratorStatusEnum::ACTIVE)
            ->where(function ($query) use ($now) {
                $query->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
            })
            ->orderBy('priority', 'desc')
            ->get();

        $exclusiveTier = $tiers->firstWhere('is_exclusive', true);
        if ($exclusiveTier) {
            $tiers = collect([$exclusiveTier]);
        } else {
            $highestPriority = $tiers->first()?->priority;
            $tiers = $tiers->where('priority', $highestPriority);
            $tiers = collect([$tiers->first()]);
        }

        $rules = collect();

        foreach ($tiers as $tier) {

            if (!$tier) {
                continue;
            }

            $tierRules = $tier->rules()
                ->where('status', PriceConfiguratorStatusEnum::ACTIVE)
                ->where('target_type', $targetType instanceof TargetTypeEnum ? $targetType->getValue() : $targetType)
                ->get();

            foreach ($tierRules as $rule) {
                if ($rule->scope == ScopeEnum::BY_CATEGORY) {
                    $categoryId = null;

                    if ($targetType == TargetTypeEnum::COURSE) {
                        $categoryId = \Botble\Courses\Models\Course::query()
                            ->where('id', $targetId)
                            ->value('category_id');
                    } elseif ($targetType == TargetTypeEnum::ROOM) {
                        $categoryId = \Botble\Hotel\Models\Room::query()
                            ->where('id', $targetId)
                            ->value('room_category_id');
                    }

                    if (!$categoryId || !in_array($categoryId, $rule->target_ids ?? [])) {
                        continue;
                    }
                } elseif ($rule->scope == ScopeEnum::SPECIFIC_PRODUCTS) {
                    if (!in_array($targetId, $rule->target_ids ?? [])) {
                        continue;
                    }
                }

                if ($customer && $rule->customer_category_id) {
                    if ($rule->customer_category_id != $customer->customer_category_id) {
                        continue;
                    }
                }

                $rules->push($rule);
            }
        }

        return $rules;
    }

    protected function applyRule(float $price, Rule $rule): float
    {
        $value = $rule->calculation_value ?? 0;

        $direction = $rule->adjustment_direction instanceof RuleDirectionEnum
            ? $rule->adjustment_direction->getValue()
            : RuleDirectionEnum::DECREASE;

        $isIncrease = $direction == RuleDirectionEnum::INCREASE;
        $isDecrease = $direction == RuleDirectionEnum::DECREASE;

        switch ($rule->calculation_type) {
            case CalculationTypeEnum::PERCENT:
                $amount = $price * ($value / 100);

                if ($isIncrease) {
                    $price = $price + $amount;
                } elseif ($isDecrease) {
                    $price = $price - $amount;
                } else {
                    $price = $price - $amount;
                }
                break;

            case CalculationTypeEnum::ABSOLUTE:
                if ($isIncrease) {
                    $price = $price + $value;
                } elseif ($isDecrease) {
                    $price = $price - $value;
                } else {
                    $price = $price - $value;
                }
                break;

            default:
                break;
        }

        if (
            $rule->rounding_mode &&
            $rule->rounding_mode != RoundingModeEnum::NONE &&
            $rule->round_to > 0 &&
            $rule->target_type != TargetTypeEnum::COURSE &&
            $rule->target_type != TargetTypeEnum::ROOM
        ) {
            $price = $this->applyRounding($price, $rule->rounding_mode, $rule->round_to);
        }

        return $price;
    }

    protected function applyRounding(float $price, RoundingModeEnum|string $mode, float $roundTo): float
    {
        $modeValue = $mode instanceof RoundingModeEnum ? $mode->getValue() : $mode;

        return match ($modeValue) {
            RoundingModeEnum::UP => ceil($price / $roundTo) * $roundTo,
            RoundingModeEnum::DOWN => floor($price / $roundTo) * $roundTo,
            RoundingModeEnum::NEAREST => round($price / $roundTo) * $roundTo,
            default => $price,
        };
    }

    protected function applyQuantityDiscount(float $price, int $hours): float
    {
        $hours = max(0, $hours);

        if ($hours <= 0) {
            return $price;
        }

        $discount = QuantityDiscount::query()
            ->where('status', PriceConfiguratorStatusEnum::ACTIVE)
            ->where('condition_type', ConditionTypeEnum::QUANTITY)
            ->where(function ($query) use ($hours) {
                $query->whereNull('range_min')
                    ->orWhere('range_min', '<=', $hours);
            })
            ->where(function ($query) use ($hours) {
                $query->whereNull('range_max')
                    ->orWhere('range_max', '>=', $hours);
            })
            ->orderByDesc('priority')
            ->orderByDesc('range_min')
            ->first();

        if (! $discount) {
            return $price;
        }

        $discountType = $discount->discount_type instanceof CalculationTypeEnum
            ? $discount->discount_type->getValue()
            : $discount->discount_type;

        $discountValue = (float) $discount->discount_value;

        $discountedPrice = match ($discountType) {
            CalculationTypeEnum::PERCENT => $price - ($price * ($discountValue / 100)),
            CalculationTypeEnum::ABSOLUTE => $price - $discountValue,
            default => $price,
        };

        return max($discountedPrice, 0);
    }

}

<?php

namespace Botble\PriceConfigurator\Services;

use Botble\Hotel\Models\Customer;
use Botble\PriceConfigurator\Enums\{
    PriceConfiguratorStatusEnum,
    ScopeEnum,
    TargetTypeEnum,
    CalculationTypeEnum,
    RoundingModeEnum
};
use Botble\PriceConfigurator\Models\{
    Rule,
    Tier,
    QuantityDiscount
};
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class PriceConfiguratorService
{
    /**
     * Hauptschnittstelle zur Preisberechnung.
     */
    public function calculatePrice(
        float $basePrice,
        TargetTypeEnum|string $targetType,
        int $targetId,
        ?Customer $customer = null,
        int $quantity = 1
    ): float {
        // NEU: Wenn Kundenkategorie unbekannt -> keinerlei Anpassungen (Originalpreis)
        if (!($customer?->customer_category_id)) {
            return max($basePrice, 0.0);
        }

        $rules = $this->getApplicableRules($targetType, $targetId, $customer);

        $price = $basePrice;

        foreach ($rules as $rule) {
            $price = $this->applyRule($price, $rule);
        }

        // Mengenrabatt aktuell nur für ROOM (Stunden-Logik)
        if ($targetType == TargetTypeEnum::ROOM) {
            $price = $this->applyQuantityDiscount($price, $quantity);
        }

        return max($price, 0.0);
    }

    /**
     * Ermittelt alle anwendbaren Rules für Target + Kunde.
     */
    protected function getApplicableRules(TargetTypeEnum|string $targetType, int $targetId, ?Customer $customer): Collection
    {
        // NEU: Ohne Kundenkategorie -> KEINE Regeln (Originalpreis)
        $customerCategoryId = $customer?->customer_category_id ? (int) $customer->customer_category_id : null;
        if (!$customerCategoryId) {
            return collect();
        }

        $now = Carbon::now();

        // Aktive Tiers im gültigen Zeitfenster
        $tiers = Tier::query()
            ->where('status', PriceConfiguratorStatusEnum::ACTIVE)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now))
            ->orderBy('priority', 'desc')
            ->get();

        if ($tiers->isEmpty()) {
            return collect();
        }

        // Exklusives Tier bevorzugen; sonst alle Tiers mit höchster Priorität zulassen
        $exclusiveTier = $tiers->firstWhere('is_exclusive', true);
        if ($exclusiveTier instanceof Tier) {
            $tiers = collect([$exclusiveTier]);
        } else {
            $highestPriority = (int) $tiers->max('priority');
            $tiers = $tiers->where('priority', $highestPriority)->values();
        }

        $rules = collect();

        // target_type vereinheitlichen (Enum|string möglich)
        $normalizedTargetType = $targetType instanceof TargetTypeEnum ? $targetType->getValue() : (string) $targetType;

        foreach ($tiers as $tier) {
            if (!($tier instanceof Tier)) {
                continue;
            }

            $tierRules = $tier->rules()
                ->where('status', PriceConfiguratorStatusEnum::ACTIVE)
                ->where('target_type', $normalizedTargetType)
                ->get();

            foreach ($tierRules as $rule) {
                // BY_CATEGORY: nur anwenden, wenn die Ziel-Kategorie enthalten ist
                if ($rule->scope == ScopeEnum::BY_CATEGORY) {
                    $categoryIdsOfTarget = $this->getCategoryIdsForTarget($targetType, $targetId);
                    $targetIdsFromRule   = (array) ($rule->target_ids ?? []);

                    if (empty($categoryIdsOfTarget) || empty(array_intersect($categoryIdsOfTarget, $targetIdsFromRule))) {
                        continue;
                    }
                }
                // SPECIFIC_PRODUCTS: nur anwenden, wenn die Ziel-ID enthalten ist
                elseif ($rule->scope == ScopeEnum::SPECIFIC_PRODUCTS) {
                    $targetIdsFromRule = (array) ($rule->target_ids ?? []);
                    if (!in_array($targetId, $targetIdsFromRule, true)) {
                        continue;
                    }
                }

                // Kundenkategorie muss EXAKT matchen (Regeln dürfen nicht "durchrutschen")
                $ruleHasCustomerCategory = !empty($rule->customer_category_id) && (int) $rule->customer_category_id > 0;
                if ($ruleHasCustomerCategory && (int) $rule->customer_category_id !== $customerCategoryId) {
                    continue;
                }
                // Regeln ohne customer_category_id gelten für alle Kategorien (hier erlaubt, da Kategorie bekannt)

                $rules->push($rule);
            }
        }

        return $rules;
    }

    /**
     * Wendet eine einzelne Rule auf den Preis an (inkl. optionalem Runden).
     */
    protected function applyRule(float $price, Rule $rule): float
    {
        $value = (float) ($rule->calculation_value ?? 0);

        switch ($rule->calculation_type) {
            case CalculationTypeEnum::PERCENT:
                $price -= $price * ($value / 100);
                break;

            case CalculationTypeEnum::ABSOLUTE:
                $price -= $value;
                break;
        }

        if (
            $rule->rounding_mode &&
            $rule->rounding_mode != RoundingModeEnum::NONE &&
            (float) $rule->round_to > 0
        ) {
            $price = $this->applyRounding($price, $rule->rounding_mode, (float) $rule->round_to);
        }

        return $price;
    }

    /**
     * Rundungslogik.
     */
    protected function applyRounding(float $price, RoundingModeEnum|string $mode, float $roundTo): float
    {
        $modeValue = $mode instanceof RoundingModeEnum ? $mode->getValue() : $mode;

        return match ($modeValue) {
            RoundingModeEnum::UP      => ceil($price / $roundTo) * $roundTo,
            RoundingModeEnum::DOWN    => floor($price / $roundTo) * $roundTo,
            RoundingModeEnum::NEAREST => round($price / $roundTo) * $roundTo,
            default                   => $price,
        };
    }

    /**
     * Mengenrabatt (z. B. Stundenstaffel).
     */
    protected function applyQuantityDiscount(float $price, int $hours): float
    {
        if ($hours <= 1) {
            return $price;
        }

        $discount = QuantityDiscount::query()
            ->where('status', PriceConfiguratorStatusEnum::ACTIVE)
            ->where(function ($query) use ($hours) {
                $query->where('range_min', '<=', $hours)
                    ->where(function ($q) use ($hours) {
                        $q->whereNull('range_max')
                          ->orWhere('range_max', '>=', $hours);
                    });
            })
            ->orderBy('priority', 'desc')
            ->first();

        if (!$discount) {
            return $price;
        }

        return match ($discount->discount_type) {
            CalculationTypeEnum::PERCENT  => $price - ($price * ((float) $discount->discount_value / 100)),
            CalculationTypeEnum::ABSOLUTE => $price - (float) $discount->discount_value,
            default                       => $price,
        };
    }

    /**
     * Liefert die Kategorie-IDs (als Array) für ein Ziel (Course/Room).
     * - COURSES: `category_id`
     * - ROOMS:   bevorzugt `room_category_id`, Fallback auf `category_id` (Schema-Check)
     */
    protected function getCategoryIdsForTarget(TargetTypeEnum|string $targetType, int $targetId): array
    {
        if ($targetType == TargetTypeEnum::COURSE) {
            $id = \Botble\Courses\Models\Course::query()
                ->where('id', $targetId)
                ->value('category_id');

            return $id ? [(int) $id] : [];
        }

        if ($targetType == TargetTypeEnum::ROOM) {
            if (Schema::hasColumn('ht_rooms', 'room_category_id')) {
                $id = \Botble\Hotel\Models\Room::query()
                    ->where('id', $targetId)
                    ->value('room_category_id');

                return $id ? [(int) $id] : [];
            }

            if (Schema::hasColumn('ht_rooms', 'category_id')) {
                $id = \Botble\Hotel\Models\Room::query()
                    ->where('id', $targetId)
                    ->value('category_id');

                return $id ? [(int) $id] : [];
            }
        }

        return [];
    }
}

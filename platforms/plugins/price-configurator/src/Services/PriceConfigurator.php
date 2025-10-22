<?php

namespace Botble\PriceConfigurator\Services;

use Botble\Hotel\Models\Room;
use Illuminate\Support\Facades\DB;

class PriceConfigurator
{
    public function adjustRoomTotalPrice(Room $room, float $baseTotal, string $customerCategory): float
    {
        $customerCategory = $customerCategory ?: 'STANDARD';

        $tiers = DB::table('pc_price_tiers')
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })
            ->orderBy('priority')
            ->get();

        $percentFactors = [];
        $absoluteAdds = [];

        foreach ($tiers as $tier) {
            $rule = DB::table('pc_price_rules')
                ->where('price_tier_id', $tier->id)
                ->where('status', 'active')
                ->where('customer_category', $customerCategory)
                ->orderBy('id')
                ->first();

            if (! $rule) {
                continue;
            }

            $matchesScope = true;
            if ($rule->scope === 'by_room_category') {
                $catId = $room->category_id ?? null;
                $allowedIds = DB::table('pc_price_rule_room_category')
                    ->where('price_rule_id', $rule->id)
                    ->pluck('room_category_id')
                    ->all();
                $matchesScope = $catId && in_array($catId, $allowedIds);
            }

            if (! $matchesScope) {
                continue;
            }

            if ($rule->calculation_type === 'percent') {
                $percentFactors[] = 1 + ((float) $rule->calculation_value) / 100.0;
            } else {
                $absoluteAdds[] = (float) $rule->calculation_value;
            }

            if ((bool) $tier->is_exclusive) {
                break;
            }
        }

        $price = $baseTotal;

        foreach ($percentFactors as $factor) {
            $price = $price * $factor;
        }

        foreach ($absoluteAdds as $add) {
            $price = $price + $add;
        }

        if ($price < 0) {
            $price = 0;
        }

        return round($price, 2);
    }
}

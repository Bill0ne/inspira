<?php

namespace Botble\Hotel\Services;

use Botble\Hotel\Enums\ServicePriceTypeEnum;
use Botble\Hotel\Facades\HotelHelper;
use Botble\Hotel\Models\Customer;
use Botble\Hotel\Models\Food;
use Botble\Hotel\Models\Room;
use Botble\Hotel\Models\Service;
use Botble\Hotel\Services\CouponService;
use Carbon\Carbon;
use DateTimeInterface;
use Throwable;

class CheckoutPricingService
{
    public function __construct(protected CouponService $couponService)
    {
    }

    public function calculateRoomPricing(
        Room $room,
        array $slots,
        int $numberOfRooms = 1,
        ?Customer $customer = null
    ): array {
        $normalizedSlots = [];

        foreach ($slots as $slot) {
            $normalized = $this->normalizeSlotForPricing($slot);

            if (! $normalized) {
                continue;
            }

            $basePrice = $room->getRoomTotalPrice(
                $normalized['start_date']->format(HotelHelper::getDateFormat()),
                $normalized['end_date']->format(HotelHelper::getDateFormat()),
                $numberOfRooms
            );

            $normalizedSlots[] = [
                'start_date' => $normalized['start_date'],
                'end_date' => $normalized['end_date'],
                'hours' => $normalized['hours'],
                'base_price' => $basePrice,
            ];
        }

        $totalBasePrice = array_sum(array_column($normalizedSlots, 'base_price'));
        $totalHours = array_sum(array_column($normalizedSlots, 'hours'));

        $totalConfiguredPrice = $totalBasePrice;

        if ($totalBasePrice > 0 && function_exists('is_plugin_active') && is_plugin_active('price-configurator')) {
            $service = app('Botble\\PriceConfigurator\\Services\\PriceConfiguratorService');

            if ($service) {
                try {
                    $totalConfiguredPrice = $service->calculatePrice(
                        $totalBasePrice,
                        \Botble\PriceConfigurator\Enums\TargetTypeEnum::ROOM,
                        $room->id,
                        $customer,
                        max($totalHours, 1)
                    );
                } catch (Throwable) {
                    $totalConfiguredPrice = $totalBasePrice;
                }
            }
        }

        $ruleDiscount = max($totalBasePrice - $totalConfiguredPrice, 0);

        if ($totalBasePrice > 0) {
            foreach ($normalizedSlots as &$slot) {
                $share = $slot['base_price'] / $totalBasePrice;
                $slot['final_price'] = max($totalConfiguredPrice * $share, 0);
            }
            unset($slot);
        } else {
            foreach ($normalizedSlots as &$slot) {
                $slot['final_price'] = 0;
            }
            unset($slot);
        }

        return [
            'slots' => $normalizedSlots,
            'total_base_price' => $totalBasePrice,
            'total_configured_price' => $totalConfiguredPrice,
            'total_hours' => $totalHours,
            'rule_discount' => $ruleDiscount,
        ];
    }

    public function summarizeAddons(
        array $serviceIds = [],
        array $foodIds = [],
        int $numberOfRooms = 1,
        int $nights = 1
    ): array {
        $serviceAmount = 0;
        $selectedServices = [];

        if (! empty($serviceIds)) {
            $services = Service::query()
                ->whereIn('id', $serviceIds)
                ->get();

            foreach ($services as $service) {
                $price = $service->price;

                if ($service->price_type == ServicePriceTypeEnum::PER_DAY) {
                    $price *= $nights;
                }

                $serviceAmount += $price;
            }

            $serviceAmount *= $numberOfRooms;
            $selectedServices = $services->pluck('id')->values()->all();
        }

        $foodAmount = 0;
        $selectedFoods = [];

        if (! empty($foodIds)) {
            $foods = Food::query()
                ->whereIn('id', $foodIds)
                ->get();

            foreach ($foods as $food) {
                $foodAmount += $food->price;
            }

            $selectedFoods = $foods->pluck('id')->values()->all();
        }

        return [
            'service_amount' => $serviceAmount,
            'selected_services' => $selectedServices,
            'food_amount' => $foodAmount,
            'selected_foods' => $selectedFoods,
        ];
    }

    public function calculateTotals(
        Room $room,
        array $slots,
        array $serviceIds = [],
        array $foodIds = [],
        int $numberOfRooms = 1,
        ?Customer $customer = null,
        ?string $couponCode = null
    ): array {
        $pricing = $this->calculateRoomPricing($room, $slots, $numberOfRooms, $customer);

        $nights = max(1, (int) ceil(max($pricing['total_hours'], 1) / 24));

        $addonSummary = $this->summarizeAddons($serviceIds, $foodIds, $numberOfRooms, $nights);

        $amount = $pricing['total_configured_price'] + $addonSummary['service_amount'] + $addonSummary['food_amount'];

        [$couponAmount, $coupon] = $this->determineCouponDiscount($couponCode, $amount);

        $quantityDiscountAmount = $this->calculateQuantityDiscount(
            $pricing['total_hours'],
            $pricing['total_configured_price']
        );

        $taxableAmount = max($amount - $couponAmount - $quantityDiscountAmount, 0);
        $taxAmount = $room->tax->percentage * $taxableAmount / 100;
        $totalAmount = $taxableAmount + $taxAmount;

        return array_merge($pricing, $addonSummary, [
            'amount' => $amount,
            'discount_amount' => $couponAmount,
            'coupon_amount' => $couponAmount,
            'coupon' => $coupon,
            'mengenrabatt_amount' => $quantityDiscountAmount,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
        ]);
    }

    protected function calculateQuantityDiscount(int $hours, float $price): float
    {
        if ($hours <= 0 || $price <= 0) {
            return 0;
        }

        $discount = \Botble\PriceConfigurator\Models\QuantityDiscount::query()
            ->where('status', 'active')
            ->where('condition_type', 'quantity')
            ->where(function ($q) use ($hours) {
                $q->whereNull('range_min')
                    ->orWhere('range_min', '<=', $hours);
            })
            ->where(function ($q) use ($hours) {
                $q->whereNull('range_max')
                    ->orWhere('range_max', '>=', $hours);
            })
            ->orderByDesc('priority')
            ->orderByDesc('range_min')
            ->first();

        if (! $discount) {
            return 0;
        }

        $value = (float) $discount->discount_value;

        return match ($discount->discount_type) {
            'percent' => $price * ($value / 100),
            'absolute' => min($value, $price),
            default => 0,
        };
    }

    public function determineCouponDiscount(?string $couponCode, float $amountTotal): array
    {
        if (! $couponCode) {
            return [0.0, null];
        }

        $coupon = $this->couponService->getCouponByCode($couponCode);

        if ($coupon === null) {
            return [0.0, null];
        }

        $discount = $this->couponService->getDiscountAmount(
            $coupon->type->getValue(),
            $coupon->value,
            $amountTotal
        );

        return [min($discount, $amountTotal), $coupon];
    }

    protected function normalizeSlotForPricing(mixed $slot): ?array
    {
        if (is_string($slot)) {
            $slot = trim($slot);

            if ($slot === '') {
                return null;
            }

            if (preg_match('/^(\d{2}\.\d{2}\.\d{4})\s+(\d{2}:\d{2})\s*-\s*(\d{2}:\d{2})$/', $slot, $matches)) {
                try {
                    $startDate = Carbon::createFromFormat('d.m.Y H:i', "{$matches[1]} {$matches[2]}");
                    $endDate = Carbon::createFromFormat('d.m.Y H:i', "{$matches[1]} {$matches[3]}");
                } catch (Throwable) {
                    return null;
                }
            } else {
                try {
                    $startDate = HotelHelper::dateFromRequest($slot);
                } catch (Throwable) {
                    return null;
                }

                $endDate = $startDate->copy()->addHour();
            }
        } elseif (is_array($slot)) {
            $startDate = $this->parseSlotDateValue($slot['start_date'] ?? $slot['start'] ?? null);
            $endDate = $this->parseSlotDateValue($slot['end_date'] ?? $slot['end'] ?? null, $startDate);
        } else {
            return null;
        }

        if (! $startDate || ! $endDate) {
            return null;
        }

        if ($endDate->lessThanOrEqualTo($startDate)) {
            $endDate = $startDate->copy()->addHour();
        }

        return [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'hours' => max(1, $endDate->diffInHours($startDate)),
        ];
    }

    protected function parseSlotDateValue(mixed $value, ?Carbon $reference = null): ?Carbon
    {
        if ($value instanceof Carbon) {
            return $value->copy();
        }

        if ($value instanceof DateTimeInterface) {
            return Carbon::instance($value);
        }

        if (is_string($value)) {
            $value = trim($value);

            if ($value === '') {
                return null;
            }

            try {
                return HotelHelper::dateFromRequest($value);
            } catch (Throwable) {
                // Continue to alternative parsing strategies
            }

            foreach (['Y-m-d H:i', 'd.m.Y H:i'] as $format) {
                try {
                    return Carbon::createFromFormat($format, $value);
                } catch (Throwable) {
                    continue;
                }
            }

            if ($reference && preg_match('/^\d{1,2}:\d{2}$/', $value)) {
                foreach (['d.m.Y', 'Y-m-d'] as $dateFormat) {
                    try {
                        return Carbon::createFromFormat(
                            $dateFormat . ' H:i',
                            $reference->format($dateFormat) . ' ' . $value
                        );
                    } catch (Throwable) {
                        continue;
                    }
                }
            }
        }

        return null;
    }
}

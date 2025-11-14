<?php

namespace Botble\Hotel\Services;

use Botble\Hotel\Models\Customer;
use Botble\Hotel\Models\CustomerCard;
use Botble\Hotel\Models\CustomerCardUsage;
use Botble\Hotel\Models\Booking;
use Botble\Courses\Models\Course;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Collection;
use Botble\Hotel\Models\CustomerCardOrder;

class CustomerCardService
{
    public function __construct(protected DatabaseManager $database)
    {
    }

    public function getActiveCardsByUser(?int $userId): Collection
    {
        return CustomerCard::query()
            ->active()
            ->when($userId, function ($query, $userId) {
                $query->where('assigned_to', $userId);
            })
            ->orderBy('name')
            ->get();
    }

    public function getApplicableCardsForCourse(?int $courseId, ?int $userId): Collection
    {
        if (! $courseId) {
            return collect();
        }

        if (class_exists(Course::class) && ! Course::query()->whereKey($courseId)->exists()) {
            return collect();
        }

        return $this->getActiveCardsByUser($userId);
    }

    public function getValidCard(int $cardId, ?int $userId): ?CustomerCard
    {
        return CustomerCard::query()
            ->active()
            ->whereKey($cardId)
            ->where(function ($query) use ($userId) {
                if ($userId) {
                    $query->where('assigned_to', $userId);
                } else {
                    $query->whereNull('assigned_to');
                }
            })
            ->first();
    }

    public function isApplicable(CustomerCard $card, ?int $courseId): bool
    {
        if (! $card->is_active || $card->units_remaining <= 0) {
            return false;
        }

        if ($card->valid_until && $card->valid_until->isPast()) {
            return false;
        }

        if (! $courseId) {
            return true;
        }

        if (! class_exists(Course::class)) {
            return true;
        }

        return Course::query()->whereKey($courseId)->exists();
    }

    public function calculateDiscount(CustomerCard $card, ?Course $course, int $units = 1, ?float $courseNetPrice = null): float
    {
        $availableUnits = max($card->units_remaining, 0);

        if ($availableUnits <= 0) {
            return 0.0;
        }

        $units = max(1, min($units, $availableUnits));
        $coursePrice = $courseNetPrice ?? ($course ? (float) $course->price : 0.0);

        if ($coursePrice <= 0) {
            $unitValue = 0;
        } else {
            $unitValue = min((float) $card->base_price, $coursePrice);
        }

        $discount = $unitValue * $units;

        return round(max($discount, 0), 2);
    }

    public function calculatePurchasePrice(CustomerCard $card): float
    {
        $total = (float) $card->base_price * max($card->units_total, 0);
        $discount = $total * ($card->discount_percent / 100);

        return round(max($total - $discount, 0), 2);
    }

    public function customerHasPurchasedTemplate(int $customerId, int $templateId): bool
    {
        return CustomerCardOrder::query()
            ->where('customer_id', $customerId)
            ->where('card_template_id', $templateId)
            ->where('status', 'completed')
            ->exists();
    }

    public function customerCanPurchaseTemplate(CustomerCard $card, Customer $customer): bool
    {
        if (! $card->is_single_purchase) {
            return true;
        }

        return ! $this->customerHasPurchasedTemplate($customer->getKey(), $card->getKey());
    }

    public function consumeUnits(CustomerCard $card, ?Booking $booking, ?Course $course, int $units, float $discountAmount): CustomerCardUsage
    {
        $units = max(0, min($units, $card->units_remaining));

        return $this->database->transaction(function () use ($card, $booking, $course, $units, $discountAmount) {
            if ($units > 0) {
                $card->decrement('units_remaining', $units);
                $card->refresh();
            }

            if ($card->units_remaining <= 0) {
                $card->update(['is_active' => false, 'units_remaining' => 0]);
            }

            if ($card->valid_until && $card->valid_until->isPast()) {
                $card->update(['is_active' => false]);
            }

            return CustomerCardUsage::query()->create([
                'card_id' => $card->getKey(),
                'booking_id' => $booking?->getKey(),
                'course_id' => $course?->getKey(),
                'units_used' => $units,
                'discount_amount' => $discountAmount,
            ]);
        });
    }

    public function restoreUnits(CustomerCard $card, int $units): CustomerCard
    {
        $units = max(0, $units);

        return $this->database->transaction(function () use ($card, $units) {
            if ($units > 0) {
                $card->increment('units_remaining', $units);
            }

            if ($card->units_remaining > 0) {
                $card->update(['is_active' => true]);
            }

            return $card->refresh();
        });
    }

    public function assignTemplateToCustomer(CustomerCard $template, Customer $customer): CustomerCard
    {
        return $this->database->transaction(function () use ($template, $customer) {
            $attributes = [
                'name' => $template->name,
                'type' => $template->type,
                'base_price' => $template->base_price,
                'discount_percent' => $template->discount_percent,
                'units_total' => $template->units_total,
                'units_remaining' => $template->units_total,
                'valid_until' => $template->valid_until,
                'is_active' => true,
                'created_by' => $template->created_by,
                'assigned_to' => $customer->getKey(),
            ];

            $existingCard = CustomerCard::query()
                ->where('assigned_to', $customer->getKey())
                ->where('name', $template->name)
                ->first();

            if ($existingCard) {
                $existingCard->fill($attributes);
                $existingCard->units_remaining = $template->units_total;
                if (! $existingCard->uid) {
                    $existingCard->uid = CustomerCard::generateUid();
                }
                $existingCard->save();

                return $existingCard->refresh();
            }

            $newCard = $template->replicate();
            $newCard->fill($attributes);
            $newCard->uid = CustomerCard::generateUid();
            $newCard->save();

            return $newCard->refresh();
        });
    }

    public function customerHasActiveCard(int $customerId, ?int $ignoreCardId = null): bool
    {
        return CustomerCard::query()
            ->active()
            ->where('assigned_to', $customerId)
            ->when($ignoreCardId, function ($query, $ignoreCardId) {
                $query->whereKeyNot($ignoreCardId);
            })
            ->exists();
    }
}

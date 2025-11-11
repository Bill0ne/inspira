<?php

namespace Botble\InspiraCancellation\Services;

use Botble\InspiraCancellation\Events\BookingCancelledEvent;
use Botble\InspiraCancellation\Models\Cancellation;
use Botble\InspiraCancellation\Models\CancellationRule;
use Botble\Hotel\Enums\BookingStatusEnum;
use Botble\Hotel\Models\Booking;
use Botble\Courses\Models\CourseBooking;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class CancellationService
{
    public function getCancellationQuote(string $type, Model $booking): array
    {
        $type = $this->normalizeType($type);

        $startDate = $this->resolveStartDate($type, $booking);

        $days = $startDate ? Carbon::now()->diffInDays($startDate, false) : null;

        $rule = $startDate ? $this->findRuleFor($type, $startDate, $days) : null;

        $total = $this->resolveTotalAmount($booking);

        $percent = $rule?->refund_percent ?? 0;
        $refund = $total * ($percent / 100);
        $refund = round($refund, 2);

        return [
            'type' => $type,
            'start_date' => $startDate,
            'days_until_start' => $days,
            'rule' => $rule,
            'refund_amount' => $refund,
            'refund_percent' => $percent,
            'total_amount' => $total,
            'fee_amount' => max($total - $refund, 0),
        ];
    }

    public function createCancellation(Model $booking, array $quote, array $context = []): Cancellation
    {
        $type = Arr::get($quote, 'type');
        $rule = Arr::get($quote, 'rule');

        $cancellation = Cancellation::query()->create([
            'booking_id' => $booking->getKey(),
            'booking_type' => $type,
            'booking_reference' => $this->resolveReference($booking),
            'customer_id' => $booking->customer_id ?? Arr::get($context, 'customer_id'),
            'rule_id' => $rule?->getKey(),
            'refund_amount' => Arr::get($quote, 'refund_amount', 0),
            'refund_percent' => Arr::get($quote, 'refund_percent', 0),
            'status' => Arr::get($context, 'status', 'pending'),
            'notes' => Arr::get($context, 'notes'),
        ]);

        if ($booking instanceof Booking || $booking instanceof CourseBooking) {
            $booking->status = BookingStatusEnum::CANCELLED;
            $booking->save();
        }

        event(new BookingCancelledEvent($booking, $cancellation, $quote));

        return $cancellation;
    }

    public function findRuleFor(string $type, CarbonInterface $startDate, ?int $days = null): ?CancellationRule
    {
        $type = $this->normalizeType($type);

        $days = $days ?? Carbon::now()->diffInDays($startDate, false);

        return CancellationRule::query()
            ->where('type', $type)
            ->where('active', true)
            ->orderBy('order')
            ->get()
            ->first(function (CancellationRule $rule) use ($days) {
                $from = $rule->from_days;
                $to = $rule->to_days;

                if (! is_null($from) && $days < $from) {
                    return false;
                }

                if (! is_null($to) && $days > $to) {
                    return false;
                }

                return true;
            });
    }

    protected function resolveStartDate(string $type, Model $booking): ?CarbonInterface
    {
        if ($type === 'course' && $booking instanceof CourseBooking) {
            $start = $booking->session?->start_date;
        } elseif ($type === 'room' && $booking instanceof Booking) {
            $start = $booking->room?->start_date;
        } else {
            $start = $booking->start_date ?? null;
        }

        if (! $start) {
            return null;
        }

        return Carbon::parse($start);
    }

    protected function resolveTotalAmount(Model $booking): float
    {
        $amount = $booking->amount ?? $booking->total_price ?? 0;

        return (float) $amount;
    }

    protected function resolveReference(Model $booking): ?string
    {
        return $booking->transaction_id ?? ($booking->booking_number ?? null);
    }

    protected function normalizeType(string $type): string
    {
        $type = Str::lower($type);

        return in_array($type, ['course', 'room'], true) ? $type : 'course';
    }
}

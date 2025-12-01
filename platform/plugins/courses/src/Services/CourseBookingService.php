<?php

namespace Botble\Courses\Services;

use Botble\Hotel\Enums\BookingStatusEnum;
use Botble\Courses\Events\CourseBookingCreated;
use Botble\Courses\Models\CourseBooking;
use Botble\Hotel\Enums\CustomerCardCoverageType;
use Botble\Hotel\Models\CustomerCard;
use Botble\Hotel\Services\CustomerCardPricingService;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;

class CourseBookingService
{
    /**
     * Verarbeitet eine Kursbuchung nach einem Payment-Event
     * (z.B. Stripe-Callback oder PAYMENT_ACTION_PAYMENT_PROCESSED).
     */
    public function processBooking(int $bookingId, ?string $chargeId = null): ?CourseBooking
    {
        /** @var CourseBooking|null $courseBooking */
        $courseBooking = CourseBooking::query()->find($bookingId);

        if (! $courseBooking) {
            return null;
        }

        // Kanal für reine Kundenkarten-Buchungen
        $customerCardMethod = (string) PaymentMethodEnum::CUSTOMER_CARD();

        /**
         * ------------------------------------------------------------------
         *  PAYMENT ERMITTELN
         * ------------------------------------------------------------------
         */
        $payment = null;

        if ($chargeId) {
            $payment = Payment::query()
                ->where(['charge_id' => $chargeId])
                ->first();
        }

        if (! $payment && $courseBooking->payment_id) {
            $payment = Payment::query()->find($courseBooking->payment_id);
        }

        if (! $payment) {
            $payment = Payment::query()
                ->where([
                    'order_id'   => $bookingId,
                    'order_type' => CourseBooking::class,
                ])
                ->latest()
                ->first();
        }

        if ($payment) {
            $courseBooking->payment_id = $payment->getKey();

            // Channel zuverlässig normalisieren (stripe, customer_card, cod, bank_transfer, …)
            $channel = $this->normalizePaymentChannel($payment->payment_channel);
            $courseBooking->payment_method = $channel;

            // Status sauber als String auflösen (z.B. "completed", "pending", …)
            $status = $this->resolveEnumValue($payment->status);

            /**
             * ------------------------------------------------------------------
             *  BOOKING STATUS HANDLING
             * ------------------------------------------------------------------
             */
            switch ($status) {
                // PAYMENT COMPLETED
                case PaymentStatusEnum::COMPLETED:
                    // Wenn Kundenkarte beteiligt und noch nicht verbraucht: erst PENDING
                    if ($courseBooking->customer_card_id && ! $courseBooking->customer_card_consumed_at) {
                        $courseBooking->status = BookingStatusEnum::PENDING;
                    } else {
                        $courseBooking->status = BookingStatusEnum::PROCESSING;
                    }
                    break;

                // PAYMENT PENDING
                case PaymentStatusEnum::PENDING:
                    if (in_array($channel, ['cod', 'bank_transfer'], true)) {
                        $courseBooking->status = BookingStatusEnum::PENDING;
                    } else {
                        $courseBooking->status = BookingStatusEnum::AWAITING_PAYMENT;
                    }
                    break;

                // PAYMENT FAILED / FRAUD / CANCELED
                case PaymentStatusEnum::FAILED:
                case PaymentStatusEnum::FRAUD:
                case PaymentStatusEnum::CANCELED:
                    $courseBooking->status = BookingStatusEnum::FAILED;
                    break;

                // PAYMENT REFUND / REFUNDING
                case PaymentStatusEnum::REFUNDING:
                case PaymentStatusEnum::REFUNDED:
                    $courseBooking->status = BookingStatusEnum::CANCELLED;
                    break;
            }

            $courseBooking->save();
        }

        /**
         * ------------------------------------------------------------------
         *  PAYMENT METHOD ZWINGEND SETZEN, WENN KARTE BENUTZT
         * ------------------------------------------------------------------
         */
        if ($courseBooking->customer_card_id && ! $courseBooking->payment_method) {
            $courseBooking->payment_method = $customerCardMethod;
            $courseBooking->save();
        }

        /**
         * ------------------------------------------------------------------
         *  COVERAGE TYPE STANDARDISIEREN
         * ------------------------------------------------------------------
         */
        if ($courseBooking->customer_card_id && ! $courseBooking->customer_card_coverage_type) {
            $courseBooking->customer_card_coverage_type = $this->normalizeCoverageType(
                CustomerCardCoverageType::PARTIAL
            );
            $courseBooking->save();
        }

        CourseBookingCreated::dispatch($courseBooking);

        return $courseBooking;
    }

    /**
     * Finalisiert die Nutzung der Kundenkarte für eine Buchung:
     * - prüft Payment-Status (bei Restzahlung)
     * - sperrt & belastet die Karte
     * - legt Usage-Eintrag an
     * - setzt Booking-Status auf PROCESSING
     */
    public function finalizeCustomerCardUsage(CourseBooking $courseBooking): void
    {
        if (! $courseBooking->exists) {
            Log::warning('[CustomerCardFinalize] Booking not persisted, skipping', [
                'booking_id' => $courseBooking->getKey(),
            ]);

            return;
        }

        if (! $courseBooking->customer_card_id) {
            Log::info('[CustomerCardFinalize] Booking has no customer card, skipping', [
                'booking_id' => $courseBooking->getKey(),
            ]);

            return;
        }

        // Mindestens 1 Einheit erforderlich
        if ($courseBooking->customer_card_units_used <= 0) {
            Log::warning('[CustomerCardFinalize] Booking ' . $courseBooking->getKey()
                . ' missing units_used, skipping');

            return;
        }

        // Bereits verbraucht? Dann nicht doppelt finalisieren
        if ($courseBooking->customer_card_consumed_at) {
            Log::info('[CustomerCardFinalize] Booking ' . $courseBooking->getKey() . ' already finalized');

            return;
        }

        $customerCardMethod = $this->normalizePaymentChannel(PaymentMethodEnum::CUSTOMER_CARD());

        // Sicherstellen, dass bei Kartennutzung ein Payment-Method gesetzt ist
        if (! $courseBooking->payment_method) {
            $courseBooking->payment_method = $customerCardMethod;
            $courseBooking->save();
        }

        /**
         * ------------------------------------------------------------------
         *  PAYMENT ERMITTELN
         * ------------------------------------------------------------------
         */
        $payment = null;
        $paymentStatus = null;

        if ($courseBooking->payment_id) {
            $payment = Payment::query()->find($courseBooking->payment_id);
        }

        if (! $payment) {
            $payment = Payment::query()
                ->where([
                    'order_id'   => $courseBooking->getKey(),
                    'order_type' => CourseBooking::class,
                ])
                ->latest()
                ->first();

            if ($payment && ! $courseBooking->payment_id) {
                $courseBooking->forceFill([
                    'payment_id'     => $payment->getKey(),
                    'payment_method' => $this->normalizePaymentChannel($payment->payment_channel),
                ])->save();
            }
        }

        if ($payment) {
            $paymentStatus = $this->resolveEnumValue($payment->status);
        }

        /**
         * ------------------------------------------------------------------
         *  FALL 1: RESTZAHLUNG > 0 → PAYMENT MUSS "completed" SEIN
         * ------------------------------------------------------------------
         */
        if ($courseBooking->amount > 0) {
            if (! $payment || $paymentStatus !== PaymentStatusEnum::COMPLETED) {
                $this->markCustomerCardFinalizePending($courseBooking);

                Log::warning('[CustomerCardFinalize] Payment not completed yet, skipping', [
                    'booking_id'     => $courseBooking->getKey(),
                    'payment_id'     => $payment?->getKey(),
                    'payment_status' => $paymentStatus,
                    'amount'         => $courseBooking->amount,
                ]);

                return;
            }
        } else {
            /**
             * ------------------------------------------------------------------
             *  FALL 2: ZERO-AMOUNT → PURE KARTENZAHLUNG
             *  Kein Completed-Payment erzwingen, aber sauber loggen.
             * ------------------------------------------------------------------
             */
            Log::info('[CustomerCardFinalize] Proceeding for zero-amount booking', [
                'booking_id'     => $courseBooking->getKey(),
                'payment_id'     => $payment?->getKey(),
                'payment_status' => $paymentStatus,
                'amount'         => $courseBooking->amount,
            ]);
        }

        // Falls Pending-Flag gesetzt war → jetzt entfernen
        if ($this->isCustomerCardFinalizePending($courseBooking)) {
            $this->clearCustomerCardFinalizePending($courseBooking);
        }

        /**
         * ------------------------------------------------------------------
         *  COVERAGE TYPE NORMALISIEREN
         * ------------------------------------------------------------------
         */
        $coverageValue = $this->normalizeCoverageType($courseBooking->customer_card_coverage_type);

        if ($courseBooking->customer_card_coverage_type !== $coverageValue) {
            $courseBooking->customer_card_coverage_type = $coverageValue;
            $courseBooking->save();
        }

        /**
         * ------------------------------------------------------------------
         *  SPLIT-BETRÄGE SCHREIBEN (KARTE VS. ONLINE)
         * ------------------------------------------------------------------
         */
        if ($courseBooking->customer_card_id) {
            $courseBooking->forceFill([
                'payment_split_card_gross'   => $courseBooking->payment_split_card_gross
                    ?? $courseBooking->customer_card_discount_gross,
                'payment_split_online_gross' => $courseBooking->payment_split_online_gross
                    ?? max(0, (float) $courseBooking->amount),
            ])->save();
        }

        Log::info('[CustomerCardFinalize] Starting usage for booking ' . $courseBooking->getKey(), [
            'status'       => $this->resolveEnumValue($courseBooking->status),
            'payment_id'   => $courseBooking->payment_id,
            'payment_meth' => $this->normalizePaymentChannel($courseBooking->payment_method),
            'card_id'      => $courseBooking->customer_card_id,
            'units_used'   => $courseBooking->customer_card_units_used,
            'coverage'     => $coverageValue,
        ]);

        /**
         * ------------------------------------------------------------------
         *  TRANSACTION: KARTE LOCKEN + UNITS PRÜFEN + FINALIZE
         * ------------------------------------------------------------------
         */
        DB::transaction(function () use ($courseBooking) {
            $card = CustomerCard::query()
                ->lockForUpdate()
                ->find($courseBooking->customer_card_id);

            if (! $card) {
                Log::warning('[CustomerCardFinalize] Card not found for booking ' . $courseBooking->getKey());

                return;
            }

            if ($courseBooking->customer_id && $card->assigned_to !== $courseBooking->customer_id) {
                Log::warning('[CustomerCardFinalize] Card assigned_to mismatch for booking ' . $courseBooking->getKey(), [
                    'card_id'          => $card->getKey(),
                    'card_assigned_to' => $card->assigned_to,
                    'booking_customer' => $courseBooking->customer_id,
                ]);

                return;
            }

            $unitsRequested = max(1, (int) $courseBooking->customer_card_units_used);

            if ($card->units_remaining < $unitsRequested) {
                Log::warning('[CustomerCardFinalize] Booking ' . $courseBooking->getKey()
                    . ' requires ' . $unitsRequested . ' units but card '
                    . $card->getKey() . ' has ' . $card->units_remaining . ' remaining');

                return;
            }

            Log::info('[CustomerCardFinalize] Finalizing usage for booking ' . $courseBooking->getKey()
                . ' with card ' . $card->getKey());

            // Diese Methode reduziert units_remaining, schreibt Usage + consumed_at usw.
            app(CustomerCardPricingService::class)->finalizeUsage($courseBooking);

            // Nach erfolgreichem Verbrauch: Status auf PROCESSING setzen
            if ($courseBooking->status !== BookingStatusEnum::PROCESSING) {
                $courseBooking->status = BookingStatusEnum::PROCESSING;
                $courseBooking->save();
            }

            Log::info('[CustomerCardFinalize] Booking ' . $courseBooking->getKey()
                . ' finalized with card ' . $card->getKey());
        });
    }

    /**
     * Markiert eine Buchung als "Finalize pending", wenn Karte verwendet,
     * aber noch kein Verbrauch durchgeführt wurde.
     */
    public function markCustomerCardFinalizePendingIfNeeded(CourseBooking $courseBooking): void
    {
        if (! $courseBooking->customer_card_id || $courseBooking->customer_card_units_used <= 0) {
            return;
        }

        if ($courseBooking->customer_card_consumed_at) {
            return;
        }

        if ($this->isCustomerCardFinalizePending($courseBooking)) {
            return;
        }

        $this->markCustomerCardFinalizePending($courseBooking);
    }

    /**
     * Normalisiert den Payment-Kanal (Enum, Objekt, String) zu einem String.
     */
    public function normalizePaymentChannel(mixed $channel): string
    {
        // Botble-Enums besitzen in der Regel getValue()
        if (is_object($channel) && method_exists($channel, 'getValue')) {
            return (string) $channel->getValue();
        }

        // Native PHP 8.1+ BackedEnum
        if ($channel instanceof \BackedEnum) {
            return (string) $channel->value;
        }

        if (is_string($channel) || is_numeric($channel)) {
            return (string) $channel;
        }

        return 'unknown';
    }

    /**
     * Liefert einen stabilen String-Wert aus Enum / Wert / Objekt.
     */
    protected function resolveEnumValue(mixed $enum, string $default = 'unknown'): string
    {
        if ($enum instanceof \BackedEnum) {
            return (string) $enum->value;
        }

        if ($enum instanceof \UnitEnum) {
            return $enum->name;
        }

        if (is_object($enum) && method_exists($enum, 'getValue')) {
            return (string) $enum->getValue();
        }

        if (is_string($enum) || is_numeric($enum)) {
            return (string) $enum;
        }

        return $default;
    }

    protected function normalizeCoverageType(mixed $coverageType): string
    {
        if ($coverageType instanceof CustomerCardCoverageType) {
            return $coverageType->value;
        }

        if (is_string($coverageType) || is_numeric($coverageType)) {
            $coverageType = (string) $coverageType;
        } else {
            $coverageType = '';
        }

        if ($coverageType === '') {
            return CustomerCardCoverageType::PARTIAL->value;
        }

        return $coverageType;
    }

    /**
     * Setzt das Flag additional_info.customer_card_finalize_pending = true.
     */
    protected function markCustomerCardFinalizePending(CourseBooking $courseBooking): void
    {
        $additionalInfo = $courseBooking->additional_info ?? [];

        if (Arr::get($additionalInfo, 'customer_card_finalize_pending') === true) {
            return;
        }

        $courseBooking->forceFill([
            'additional_info' => Arr::set($additionalInfo, 'customer_card_finalize_pending', true),
        ])->save();
    }

    /**
     * Entfernt das Flag additional_info.customer_card_finalize_pending.
     */
    protected function clearCustomerCardFinalizePending(CourseBooking $courseBooking): void
    {
        $additionalInfo = $courseBooking->additional_info ?? [];

        if (! Arr::get($additionalInfo, 'customer_card_finalize_pending')) {
            return;
        }

        Arr::forget($additionalInfo, 'customer_card_finalize_pending');

        $courseBooking->forceFill([
            'additional_info' => $additionalInfo,
        ])->save();
    }

    /**
     * Prüft, ob das Finalize-Pending-Flag gesetzt ist.
     */
    protected function isCustomerCardFinalizePending(CourseBooking $courseBooking): bool
    {
        return Arr::get($courseBooking->additional_info ?? [], 'customer_card_finalize_pending', false) === true;
    }
}

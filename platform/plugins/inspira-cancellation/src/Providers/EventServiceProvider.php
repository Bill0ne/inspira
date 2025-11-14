<?php

namespace Botble\InspiraCancellation\Providers;

use Botble\Base\Facades\EmailHandler;
use Botble\InspiraCancellation\Enums\CancellationStatusEnum;
use Botble\InspiraCancellation\Events\BookingCancelledEvent;
use Botble\InspiraCancellation\Events\BookingTransferredEvent;
use Botble\InspiraCancellation\Events\CancellationRefundApprovedEvent;
use Botble\InspiraCancellation\Events\CancellationRefundPaidEvent;
use Botble\InspiraCancellation\Events\CancellationRejectedEvent;
use Botble\InspiraCancellation\Events\TransferRejectedEvent;
use Botble\InspiraCancellation\Events\TransferRequestedEvent;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class EventServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app['events']->listen(BookingCancelledEvent::class, function (BookingCancelledEvent $event): void {
            $booking = $event->booking;
            $quote = $event->quote;
            $rule = $event->cancellation->rule;

            $customerEmail = $booking->address?->email ?? $booking->customer?->email;

            $customerName = trim(implode(' ', array_filter([
                $booking->address?->first_name ?? $booking->customer?->first_name,
                $booking->address?->last_name ?? $booking->customer?->last_name,
            ])));

            $variables = [
                'booking_reference' => $event->cancellation->booking_reference,
                'total_amount' => format_price($quote['total_amount']),
                'refund_amount' => format_price($event->cancellation->refund_amount),
                'refund_percent' => $event->cancellation->refund_percent,
                'fee_amount' => format_price($quote['fee_amount']),
                'rule_description' => $rule?->description,
                'booking_type' => Str::title($event->cancellation->booking_type),
                'days_until_start' => $quote['days_until_start'],
                'customer_name' => $customerName,
                'customer_email' => $customerEmail,
                'notes' => $event->cancellation->notes,
            ];

            EmailHandler::setModule('inspira-cancellation')->setVariableValues($variables);

            if ($customerEmail) {
                EmailHandler::sendUsingTemplate('storno_customer', $customerEmail);
            }

            EmailHandler::sendUsingTemplate('storno_admin');
        });

        $this->app['events']->listen(BookingTransferredEvent::class, function (BookingTransferredEvent $event): void {
            $booking = $event->booking;
            $newCustomer = $event->newCustomer;

            $variables = [
                'booking_reference' => $booking->transaction_id ?? $booking->booking_number,
                'booking_type' => Str::title($event->log->booking_type),
                'new_customer_name' => trim($newCustomer->first_name . ' ' . $newCustomer->last_name),
                'new_customer_email' => $newCustomer->email,
            ];

            EmailHandler::setModule('inspira-cancellation')->setVariableValues($variables);

            if ($event->oldCustomerId && $event->oldCustomerEmail) {
                EmailHandler::sendUsingTemplate('replacement_old_customer', $event->oldCustomerEmail);
            }

            if ($newCustomer->email) {
                EmailHandler::sendUsingTemplate('replacement_new_customer', $newCustomer->email);
            }
        });

        $this->app['events']->listen(TransferRequestedEvent::class, function (TransferRequestedEvent $event): void {
            $booking = $event->booking;
            $payload = $event->payload;

            $requester = method_exists($booking, 'customer') ? $booking->customer()->first() : null;

            $variables = [
                'booking_reference' => $booking->transaction_id ?? $booking->booking_number ?? $event->log->booking_id,
                'booking_type' => Str::title($event->log->booking_type),
                'new_customer_name' => trim(($payload['first_name'] ?? '') . ' ' . ($payload['last_name'] ?? '')),
                'new_customer_email' => $payload['email'] ?? null,
                'requested_by' => $requester ? trim($requester->first_name . ' ' . $requester->last_name) : null,
                'requested_email' => $requester?->email,
            ];

            EmailHandler::setModule('inspira-cancellation')->setVariableValues($variables);

            EmailHandler::sendUsingTemplate('replacement_admin_request');
        });

        $this->app['events']->listen(TransferRejectedEvent::class, function (TransferRejectedEvent $event): void {
            $customer = $event->customer;
            $email = $customer?->email;

            if (! $email) {
                return;
            }

            $variables = [
                'booking_reference' => $event->booking->transaction_id ?? $event->booking->booking_number ?? $event->log->booking_id,
                'booking_type' => Str::title($event->log->booking_type),
                'customer_name' => trim($customer->first_name . ' ' . $customer->last_name),
            ];

            EmailHandler::setModule('inspira-cancellation')->setVariableValues($variables);

            EmailHandler::sendUsingTemplate('replacement_rejected_customer', $email);
        });

        $this->app['events']->listen(CancellationRefundApprovedEvent::class, function (CancellationRefundApprovedEvent $event): void {
            $cancellation = $event->cancellation->loadMissing(['customer', 'approver']);

            $email = $cancellation->customer?->email;

            if (! $email) {
                return;
            }

            $variables = [
                'booking_reference' => $cancellation->booking_reference,
                'booking_type' => Str::title($cancellation->booking_type),
                'refund_amount' => format_price($cancellation->refund_amount),
                'refund_percent' => $cancellation->refund_percent,
                'customer_name' => trim($cancellation->customer?->first_name . ' ' . $cancellation->customer?->last_name),
                'refund_status' => $cancellation->status instanceof CancellationStatusEnum ? $cancellation->status->label() : null,
                'approver_name' => $cancellation->approver?->name,
                'refund_approved_at' => optional($cancellation->approved_at)->format('d.m.Y H:i'),
            ];

            EmailHandler::setModule('inspira-cancellation')->setVariableValues($variables);

            EmailHandler::sendUsingTemplate('storno_refund_approved', $email);
        });

        $this->app['events']->listen(CancellationRefundPaidEvent::class, function (CancellationRefundPaidEvent $event): void {
            $cancellation = $event->cancellation->loadMissing(['customer', 'refunder']);

            $email = $cancellation->customer?->email;

            if (! $email) {
                return;
            }

            $variables = [
                'booking_reference' => $cancellation->booking_reference,
                'booking_type' => Str::title($cancellation->booking_type),
                'refund_amount' => format_price($cancellation->refund_amount),
                'refund_percent' => $cancellation->refund_percent,
                'customer_name' => trim($cancellation->customer?->first_name . ' ' . $cancellation->customer?->last_name),
                'refund_status' => $cancellation->status instanceof CancellationStatusEnum ? $cancellation->status->label() : null,
                'approver_name' => $cancellation->refunder?->name,
                'refund_paid_at' => optional($cancellation->refunded_at)->format('d.m.Y H:i'),
            ];

            EmailHandler::setModule('inspira-cancellation')->setVariableValues($variables);

            EmailHandler::sendUsingTemplate('storno_refund_paid', $email);
        });

        $this->app['events']->listen(CancellationRejectedEvent::class, function (CancellationRejectedEvent $event): void {
            $cancellation = $event->cancellation->loadMissing(['customer', 'approver']);

            $email = $cancellation->customer?->email;

            if (! $email) {
                return;
            }

            $variables = [
                'booking_reference' => $cancellation->booking_reference,
                'booking_type' => Str::title($cancellation->booking_type),
                'refund_amount' => format_price($cancellation->refund_amount),
                'refund_percent' => $cancellation->refund_percent,
                'customer_name' => trim($cancellation->customer?->first_name . ' ' . $cancellation->customer?->last_name),
                'approver_name' => $cancellation->approver?->name,
            ];

            EmailHandler::setModule('inspira-cancellation')->setVariableValues($variables);

            EmailHandler::sendUsingTemplate('storno_rejected', $email);
        });
    }
}

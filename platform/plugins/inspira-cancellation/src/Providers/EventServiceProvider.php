<?php

namespace Botble\InspiraCancellation\Providers;

use Botble\Base\Facades\EmailHandler;
use Botble\InspiraCancellation\Events\BookingCancelledEvent;
use Botble\InspiraCancellation\Events\BookingTransferredEvent;
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
    }
}

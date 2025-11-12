<?php

namespace Botble\Hotel\Services;

use Botble\Hotel\Models\CustomerCard;
use Botble\Hotel\Models\CustomerCardOrder;
use Botble\Hotel\Models\Customer;
use Botble\Hotel\Services\CustomerCardService;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Models\Payment;
use Illuminate\Support\Str;

class CustomerCardPurchaseService
{
    public function __construct(protected CustomerCardService $customerCardService)
    {
    }

    public function createOrder(CustomerCard $template, Customer $customer, float $amount): CustomerCardOrder
    {
        return CustomerCardOrder::query()->create([
            'customer_id' => $customer->getKey(),
            'card_template_id' => $template->getKey(),
            'amount' => $amount,
            'status' => 'pending',
            'transaction_id' => $this->generateTransactionId(),
        ]);
    }

    public function markAsFailed(CustomerCardOrder $order): CustomerCardOrder
    {
        $order->update(['status' => 'failed']);

        return $order->refresh();
    }

    public function attachPayment(CustomerCardOrder $order, ?int $paymentId): CustomerCardOrder
    {
        if ($paymentId) {
            $order->payment_id = $paymentId;
            $order->save();
        }

        return $order->refresh();
    }

    public function completeOrder(?int $orderId, ?string $chargeId = null): ?CustomerCardOrder
    {
        $payment = null;

        if (! $orderId && $chargeId) {
            $payment = Payment::query()->where('charge_id', $chargeId)->first();

            if ($payment && $payment->order_id) {
                $orderId = (int) $payment->order_id;
            }
        }

        if (! $orderId) {
            return null;
        }

        $order = CustomerCardOrder::query()->find($orderId);

        if (! $order) {
            return null;
        }

        if ($chargeId && ! $payment) {
            $payment = Payment::query()->where('charge_id', $chargeId)->first();
        }

        if ($payment) {
            return $this->handlePayment($order, $payment);
        }

        return $this->finalizeOrder($order);
    }

    public function handlePayment(CustomerCardOrder $order, Payment $payment): CustomerCardOrder
    {
        $this->attachPayment($order, $payment->getKey());

        switch ($payment->status) {
            case PaymentStatusEnum::COMPLETED:
                return $this->finalizeOrder($order);

            case PaymentStatusEnum::PENDING:
                $order->update(['status' => 'pending']);

                return $order->refresh();

            case PaymentStatusEnum::FAILED:
            case PaymentStatusEnum::FRAUD:
            case PaymentStatusEnum::CANCELED:
                return $this->markAsFailed($order);

            case PaymentStatusEnum::REFUNDING:
            case PaymentStatusEnum::REFUNDED:
                $order->update(['status' => 'refunded']);

                return $order->refresh();

            default:
                return $order->refresh();
        }
    }

    public function finalizeOrder(CustomerCardOrder $order): CustomerCardOrder
    {
        if ($order->status === 'completed') {
            return $order;
        }

        $template = $order->template;
        $customer = $order->customer;

        if (! $template || ! $customer) {
            return $this->markAsFailed($order);
        }

        $assignedCard = $this->customerCardService->assignTemplateToCustomer($template, $customer);

        $order->update([
            'status' => 'completed',
            'assigned_card_id' => $assignedCard->getKey(),
            'completed_at' => now(),
        ]);

        return $order->refresh();
    }

    protected function generateTransactionId(): string
    {
        do {
            $transactionId = strtoupper(Str::random(12));
        } while (CustomerCardOrder::query()->where('transaction_id', $transactionId)->exists());

        return $transactionId;
    }
}

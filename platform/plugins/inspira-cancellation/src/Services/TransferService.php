<?php

namespace Botble\InspiraCancellation\Services;

use Botble\Hotel\Models\Booking;
use Botble\InspiraCancellation\Enums\TransferStatusEnum;
use Botble\InspiraCancellation\Events\BookingTransferredEvent;
use Botble\InspiraCancellation\Events\TransferRejectedEvent;
use Botble\InspiraCancellation\Events\TransferRequestedEvent;
use Botble\InspiraCancellation\Models\TransferLog;
use Botble\Courses\Models\CourseBooking;
use Botble\Hotel\Models\Customer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransferService
{
    public function request(Model $booking, string $type, array $payload): TransferLog
    {
        $type = $this->normalizeType($type);

        return DB::transaction(function () use ($booking, $type, $payload) {
            $log = TransferLog::query()->create([
                'booking_id' => $booking->getKey(),
                'booking_type' => $type,
                'old_customer_id' => $booking->customer_id,
                'status' => TransferStatusEnum::PENDING,
                'payload' => Arr::only($payload, [
                    'first_name',
                    'last_name',
                    'email',
                    'phone',
                ]),
                'requested_by' => $booking->customer_id,
            ]);

            event(new TransferRequestedEvent($booking, $log, $payload));

            return $log;
        });
    }

    public function approve(TransferLog $log, ?int $userId = null): TransferLog
    {
        if ($log->status instanceof TransferStatusEnum && ! $log->status->equals(TransferStatusEnum::PENDING())) {
            return $log;
        }

        return DB::transaction(function () use ($log, $userId) {
            $booking = $this->resolveBookingFromLog($log);

            $oldCustomer = method_exists($booking, 'customer') ? $booking->customer()->first() : null;

            $payload = $log->payload ?? [];

            $customer = $this->resolveCustomer($payload);

            $booking->customer_id = $customer->getKey();
            $booking->save();

            if (method_exists($booking, 'setRelation')) {
                $booking->setRelation('customer', $customer);
            }

            $this->syncContactInformation($booking, $payload);

            $log->forceFill([
                'new_customer_id' => $customer->getKey(),
                'status' => TransferStatusEnum::APPROVED,
                'approved_at' => now(),
                'approved_by' => $userId,
            ]);

            $log->save();

            event(new BookingTransferredEvent($booking, $customer, $log->old_customer_id, $log, $oldCustomer?->email));

            return $log;
        });
    }

    public function reject(TransferLog $log, ?int $userId = null): TransferLog
    {
        if ($log->status instanceof TransferStatusEnum && ! $log->status->equals(TransferStatusEnum::PENDING())) {
            return $log;
        }

        $log->forceFill([
            'status' => TransferStatusEnum::REJECTED,
            'approved_at' => now(),
            'approved_by' => $userId,
        ]);

        $log->save();

        $booking = $this->resolveBookingFromLog($log);

        $customer = method_exists($booking, 'customer') ? $booking->customer()->first() : null;

        event(new TransferRejectedEvent($booking, $log, $customer));

        return $log;
    }

    protected function resolveCustomer(array $payload): Customer
    {
        $email = Arr::get($payload, 'email');

        $customer = Customer::query()->firstOrNew(['email' => $email]);

        $customer->fill([
            'first_name' => Arr::get($payload, 'first_name'),
            'last_name' => Arr::get($payload, 'last_name'),
            'phone' => Arr::get($payload, 'phone'),
        ]);

        $customer->save();

        return $customer;
    }

    protected function resolveBookingFromLog(TransferLog $log): Model
    {
        return match ($log->booking_type) {
            'room' => Booking::query()
                ->with(['customer', 'address'])
                ->findOrFail($log->booking_id),
            default => CourseBooking::query()
                ->with(['customer', 'address', 'session'])
                ->findOrFail($log->booking_id),
        };
    }

    protected function syncContactInformation(Model $booking, array $payload): void
    {
        if (! method_exists($booking, 'address')) {
            return;
        }

        $address = $booking->address;

        if (! $address) {
            return;
        }

        $address->first_name = Arr::get($payload, 'first_name');
        $address->last_name = Arr::get($payload, 'last_name');
        $address->email = Arr::get($payload, 'email');

        if ($address->isFillable('phone')) {
            $address->phone = Arr::get($payload, 'phone');
        }

        $address->save();

        if (method_exists($booking, 'setRelation')) {
            $booking->setRelation('address', $address);
        }
    }

    protected function normalizeType(string $type): string
    {
        $type = Str::lower($type);

        return in_array($type, ['course', 'room'], true) ? $type : 'course';
    }
}

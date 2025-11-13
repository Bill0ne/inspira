<?php

namespace Botble\InspiraCancellation\Services;

use Botble\InspiraCancellation\Events\BookingTransferredEvent;
use Botble\InspiraCancellation\Models\TransferLog;
use Botble\Courses\Models\CourseBooking;
use Botble\Hotel\Models\Customer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransferService
{
    public function transfer(Model $booking, string $type, array $payload): TransferLog
    {
        $type = $this->normalizeType($type);

        return DB::transaction(function () use ($booking, $type, $payload) {
            $customer = $this->resolveCustomer($payload);

            $oldCustomerId = $booking->customer_id;
            $oldCustomer = method_exists($booking, 'customer') ? $booking->customer()->first() : null;

            $booking->customer_id = $customer->getKey();
            $booking->save();

            if (method_exists($booking, 'setRelation')) {
                $booking->setRelation('customer', $customer);
            }

            $this->syncContactInformation($booking, $payload);

            $log = TransferLog::query()->create([
                'booking_id' => $booking->getKey(),
                'booking_type' => $type,
                'old_customer_id' => $oldCustomerId,
                'new_customer_id' => $customer->getKey(),
            ]);

            event(new BookingTransferredEvent($booking, $customer, $oldCustomerId, $log, $oldCustomer?->email));

            return $log;
        });
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

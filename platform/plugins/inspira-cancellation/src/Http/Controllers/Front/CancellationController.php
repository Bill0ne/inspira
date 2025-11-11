<?php

namespace Botble\InspiraCancellation\Http\Controllers\Front;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Courses\Models\CourseBooking;
use Botble\Hotel\Enums\BookingStatusEnum;
use Botble\Hotel\Models\Booking;
use Botble\InspiraCancellation\Facades\InspiraCancellation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CancellationController extends BaseController
{
    public function store(Request $request, string $type, int $booking)
    {
        $request->validate([
            'accept_terms' => ['accepted'],
            'notes' => ['nullable', 'string'],
        ]);

        $type = Str::lower($type);
        $model = $this->resolveBooking($type, $booking);

        abort_if(
            $model->status === BookingStatusEnum::CANCELLED,
            422,
            trans('plugins/inspira-cancellation::cancellation.messages.cancellation_already_processed')
        );

        $quote = InspiraCancellation::getCancellationQuote($type, $model);

        if (! is_null($quote['days_until_start']) && $quote['days_until_start'] < 0) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(trans('plugins/inspira-cancellation::cancellation.messages.event_in_past'));
        }

        $cancellation = InspiraCancellation::createCancellation($model, $quote, [
            'customer_id' => auth('customer')->id(),
            'notes' => $request->string('notes')->toString(),
            'status' => 'pending_refund',
        ]);

        return $this
            ->httpResponse()
            ->setMessage(trans('plugins/inspira-cancellation::cancellation.messages.cancellation_success'))
            ->setData([
                'refund_amount' => $cancellation->refund_amount,
                'refund_percent' => $cancellation->refund_percent,
            ]);
    }

    protected function resolveBooking(string $type, int $id)
    {
        $customerId = auth('customer')->id();

        if ($type === 'room') {
            $booking = Booking::query()
                ->with(['room', 'address'])
                ->where('customer_id', $customerId)
                ->findOrFail($id);
        } else {
            $booking = CourseBooking::query()
                ->with(['session', 'address'])
                ->where('customer_id', $customerId)
                ->findOrFail($id);
        }

        return $booking;
    }
}

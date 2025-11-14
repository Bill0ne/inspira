<?php

namespace Botble\InspiraCancellation\Http\Controllers\Front;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Courses\Models\CourseBooking;
use Botble\InspiraCancellation\Services\TransferService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TransferController extends BaseController
{
    public function store(Request $request, string $type, int $booking)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:60'],
        ]);

        $type = Str::lower($type);
        abort_if($type !== 'course', 404);

        $model = CourseBooking::query()
            ->with(['session', 'address'])
            ->where('customer_id', auth('customer')->id())
            ->findOrFail($booking);

        app(TransferService::class))->request($model, $type, $request->only([
            'first_name',
            'last_name',
            'email',
            'phone',
        ]));

        return $this
            ->httpResponse()
            ->setMessage(trans('plugins/inspira-cancellation::cancellation.messages.replacement_pending'));
    }
}

<div class="table-responsive">
    <table class="table table-bordered">
        <tbody>
            <tr>
                <th class="text-start">{{ trans('plugins/hotel::booking.manual_booking') }}</th>
                <td>{{ $target }}</td>
            </tr>
            <tr>
                <th class="text-start">{{ trans('plugins/hotel::booking.start_date') }}</th>
                <td>{{ BaseHelper::formatDateTime($booking->start_at) }}</td>
            </tr>
            <tr>
                <th class="text-start">{{ trans('plugins/hotel::booking.end_date') }}</th>
                <td>{{ BaseHelper::formatDateTime($booking->end_at) }}</td>
            </tr>
            <tr>
                <th class="text-start">{{ trans('plugins/hotel::booking.manual_booking_reason') }}</th>
                <td>{{ $booking->reason ?: '-' }}</td>
            </tr>
        </tbody>
    </table>
</div>

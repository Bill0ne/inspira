<div class="table-responsive">
    <table class="table table-striped align-middle mb-0">
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('Datum') }}</th>
                <th>{{ __('Kurs') }}</th>
                <th>{{ trans('plugins/hotel::customer-card.purchase.units_used') }}</th>
                <th>{{ trans('plugins/hotel::customer-card.purchase.saved_amount') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($usages as $index => $usage)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $usage->created_at->translatedFormat('d.m.Y H:i') }}</td>
                    <td>
                        @if ($usage->course)
                            {{ $usage->course->name }}
                        @elseif ($usage->booking)
                            {{ optional(optional($usage->booking)->room)->room->name ?? trans('plugins/hotel::customer-card.table.room_booking_fallback') }}
                        @else
                            —
                        @endif
                    </td>
                    <td>{{ $usage->units_used }}</td>
                    <td>{{ format_price($usage->discount_amount) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">{{ __('Keine Verwendungen gefunden.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

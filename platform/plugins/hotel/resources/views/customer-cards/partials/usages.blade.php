@if (isset($orders) && $orders->isNotEmpty())
    <div class="mb-4">
        <h5 class="mb-3">{{ trans('plugins/hotel::customer-card.table.assignments_title') }}</h5>

        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ trans('plugins/hotel::customer-card.table.customer') }}</th>
                        <th>{{ trans('plugins/hotel::customer-card.table.status') }}</th>
                        <th>{{ trans('plugins/hotel::customer-card.table.units_remaining') }}</th>
                        <th>{{ trans('plugins/hotel::customer-card.table.last_usage') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $index => $order)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div class="fw-semibold">{{ $order->customer?->name ?? $order->customer?->email ?? '—' }}</div>
                                @if ($order->customer?->email)
                                    <div class="text-muted">{{ $order->customer->email }}</div>
                                @endif
                            </td>
                            <td>
                                @if ($order->assignedCard)
                                    <span class="badge badge-{{ $order->assignedCard->status_color ?? $order->assignedCard->getStatusColorAttribute() }}">
                                        {{ $order->assignedCard->status_label }}
                                    </span>
                                @else
                                    <span class="badge badge-light">{{ trans('plugins/hotel::customer-card.table.not_assigned_anymore') }}</span>
                                @endif
                            </td>
                            <td>
                                @if ($order->assignedCard)
                                    {{ sprintf('%d / %d', $order->assignedCard->units_remaining, $order->assignedCard->units_total) }}
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                @php
                                    $latestUsage = optional($order->assignedCard?->usages->first());
                                @endphp
                                {{ $latestUsage?->created_at?->translatedFormat('d.m.Y H:i') ?? '—' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

<div class="table-responsive">
    <table class="table table-striped align-middle mb-0">
        <thead>
            <tr>
                <th>#</th>
                <th>{{ trans('plugins/hotel::customer-card.table.customer') }}</th>
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
                    <td>
                        {{ optional($usage->card->customer)->name ?? optional($usage->card->customer)->email ?? '—' }}
                    </td>
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
                    <td colspan="6" class="text-center text-muted py-4">{{ __('Keine Verwendungen gefunden.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

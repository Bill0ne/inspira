@extends(HotelHelper::viewPath('customers.master'))

@section('content')
    @php
        $customer = auth('customer')->user();
        $statusCard = $activeCard ?? null;
        $displayCard = $displayCard ?? null;
        $statusLabel = $statusCard?->status_label ?? trans('plugins/hotel::customer-card.status.inactive');
        $statusTone = $statusCard?->status_color ?? 'secondary';
        $badgeTone = match ($statusTone) {
            'success' => 'success',
            'warning' => 'warning',
            'danger' => 'danger',
            default => 'muted',
        };
        $remainingUnits = $displayCard?->units_remaining ?? 0;
        $totalUnits = $displayCard?->units_total ?? 0;
        $validUntil = $displayCard?->valid_until;
        $hasActiveCard = $hasActiveCard ?? false;
    @endphp

    

    <div class="inspira-card-hub">
        <div class="row g-4 align-items-stretch">
            <div class="col-lg-5">
                <div class="inspira-card-panel h-100">
                    <div class="inspira-card-panel__header">
                        <div>
                            <h2 class="inspira-card-panel__title">{{ trans('plugins/hotel::customer-card.purchase.card_title') }}</h2>
                            <p class="inspira-card-panel__subtitle">{{ trans('plugins/hotel::customer-card.purchase.card_subtitle') }}</p>
                        </div>
                        <span class="inspira-badge inspira-badge--{{ $badgeTone }}">{{ $statusLabel }}</span>
                    </div>

                    <div class="inspira-card-panel__body">
                        <div class="inspira-card-visual">
                            <div class="inspira-card-visual__headline">
                                {{ $displayCard?->name ?? trans('plugins/hotel::customer-card.purchase.placeholder_name') }}
                                @if ($displayCard?->uid)
                                    <span class="inspira-card-visual__uid">{{ $displayCard->uid }}</span>
                                @endif
                            </div>
                            <div class="inspira-card-visual__owner">{{ $customer->name }}</div>
                            <div class="inspira-card-visual__units">
                                <span>{{ trans('plugins/hotel::customer-card.purchase.balance_label') }}</span>
                                <strong>{{ $remainingUnits }} / {{ $totalUnits }}</strong>
                            </div>
                            <div class="inspira-card-visual__meta">
                                <span>{{ trans('plugins/hotel::customer-card.purchase.valid_until') }}</span>
                                <strong>{{ $validUntil ? $validUntil->translatedFormat('d.m.Y') : trans('plugins/hotel::customer-card.purchase.no_expiry') }}</strong>
                            </div>
                        </div>

                        @if (! $statusCard)
                            <p class="inspira-card-panel__hint">{{ trans('plugins/hotel::customer-card.purchase.no_active_hint') }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="inspira-card-panel h-100">
                    <div class="inspira-card-market__header">
                        <h2 class="inspira-card-market__title">{{ trans('plugins/hotel::customer-card.purchase.marketplace_title') }}</h2>
                        <p class="inspira-card-market__description">{{ trans('plugins/hotel::customer-card.purchase.marketplace_subtitle') }}</p>
                        @if ($hasActiveCard)
                            <div class="alert alert-warning mt-2 mb-0" role="status">
                                {{ trans('plugins/hotel::customer-card.purchase.already_active') }}
                            </div>
                        @endif
                    </div>

                    @if ($availableCards->isEmpty())
                        <div class="inspira-card-market__empty">
                            {{ trans('plugins/hotel::customer-card.purchase.empty_marketplace') }}
                        </div>
                    @else
                        <div class="row g-3">
                            @foreach ($availableCards as $card)
                                <div class="col-md-6">
                                    <div class="inspira-card-offer">
                                        <div class="inspira-card-offer__head">
                                            <h3 class="inspira-card-offer__name">{{ $card->name }}</h3>
                                            <span class="inspira-card-offer__badge">-{{ number_format($card->discount_percent, 2) }}%</span>
                                        </div>

                                        <ul class="inspira-card-offer__list">
                                            <li>{{ trans('plugins/hotel::customer-card.purchase.units_included', ['units' => $card->units_total]) }}</li>
                                            <li>{{ trans('plugins/hotel::customer-card.purchase.base_price_each', ['price' => format_price($card->base_price)]) }}</li>
                                            <li>{{ trans('plugins/hotel::customer-card.purchase.discount_note', ['percent' => number_format($card->discount_percent, 2)]) }}</li>
                                            <li>{{ trans('plugins/hotel::customer-card.purchase.valid_until_inline', ['date' => $card->valid_until ? $card->valid_until->translatedFormat('d.m.Y') : trans('plugins/hotel::customer-card.purchase.no_expiry')]) }}</li>
                                        </ul>

                                        <div>
                                            <div class="inspira-card-offer__price">{{ format_price($card->purchase_price ?? 0) }}</div>
                                            <p class="inspira-card-offer__hint text-muted mb-0">{{ trans('plugins/hotel::customer-card.purchase.price_hint') }}</p>
                                        </div>

                                        @if ($hasActiveCard)
                                            <button class="btn btn-outline-secondary w-100" type="button" disabled>
                                                {{ trans('plugins/hotel::customer-card.purchase.already_active') }}
                                            </button>
                                        @else
                                            <a class="btn btn-primary w-100" href="{{ route('customer.cards.checkout', $card) }}">
                                                {{ trans('plugins/hotel::customer-card.purchase.buy_button') }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="inspira-card-history">
            <div class="inspira-card-history__header">
                <h3 class="inspira-card-history__title">{{ trans('plugins/hotel::customer-card.purchase.history_title') }}</h3>
                <p class="inspira-card-note text-muted mb-0">{{ trans('plugins/hotel::customer-card.purchase.history_subtitle') }}</p>
            </div>

            @if ($usages->isEmpty())
                <p class="inspira-card-note text-muted mb-0">{{ trans('plugins/hotel::customer-card.purchase.history_empty') }}</p>
            @else
                <div class="table-responsive">
                    <table class="table table-striped align-middle mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('Datum') }}</th>
                                <th>{{ __('Karte') }}</th>
                                <th>{{ __('Kurs') }}</th>
                                <th>{{ trans('plugins/hotel::customer-card.purchase.units_used') }}</th>
                                <th>{{ trans('plugins/hotel::customer-card.purchase.saved_amount') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($usages as $usage)
                                <tr>
                                    <td>{{ $usage->created_at->translatedFormat('d.m.Y H:i') }}</td>
                                    <td>{{ $usage->card?->name }}</td>
                                    <td>{{ $usage->course?->name ?? __('Kurs entfernt') }}</td>
                                    <td>{{ $usage->units_used }}</td>
                                    <td>{{ format_price($usage->discount_amount) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection

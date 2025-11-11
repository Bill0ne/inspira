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
    @endphp

    <style>
        .inspira-card-hub {
            display: flex;
            flex-direction: column;
            gap: 32px;
        }

        .inspira-card-panel,
        .inspira-card-offer,
        .inspira-card-history {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            padding: 28px;
        }

        .inspira-card-panel__header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
        }

        .inspira-card-panel__title {
            font-size: 22px;
            font-weight: 600;
            margin: 0 0 4px;
        }

        .inspira-card-panel__subtitle {
            color: #6c757d;
            margin: 0;
            font-size: 14px;
        }

        .inspira-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .inspira-badge--success {
            background: #e5f6f2;
            color: #1e7d6d;
        }

        .inspira-badge--warning {
            background: #fff4e6;
            color: #c97a00;
        }

        .inspira-badge--danger {
            background: #fdeaea;
            color: #bb2d3b;
        }

        .inspira-badge--muted {
            background: #f1f3f5;
            color: #495057;
        }

        .inspira-card-panel__body {
            margin-top: 24px;
        }

        .inspira-card-visual {
            background: #f3f8f7;
            border-radius: 18px;
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 14px;
            min-height: 220px;
        }

        .inspira-card-visual__headline {
            font-size: 20px;
            font-weight: 600;
            color: #1e7d6d;
        }

        .inspira-card-visual__owner {
            font-size: 16px;
            font-weight: 500;
            color: #1f2d3d;
        }

        .inspira-card-visual__units,
        .inspira-card-visual__meta {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            color: #495057;
        }

        .inspira-card-panel__hint {
            margin-top: 18px;
            font-size: 13px;
            color: #6c757d;
        }

        .inspira-card-market__header {
            margin-bottom: 18px;
        }

        .inspira-card-market__title {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .inspira-card-market__description {
            color: #6c757d;
            margin: 0;
        }

        .inspira-card-offer {
            height: 100%;
            display: flex;
            flex-direction: column;
            gap: 18px;
            border: 1px solid #e9ecef;
        }

        .inspira-card-offer__head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .inspira-card-offer__name {
            font-size: 18px;
            font-weight: 600;
            margin: 0;
        }

        .inspira-card-offer__badge {
            background: #578E88;
            color: #fff;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 12px;
        }

        .inspira-card-offer__list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 6px;
            color: #495057;
            font-size: 14px;
        }

        .inspira-card-offer__price {
            font-size: 26px;
            font-weight: 600;
            color: #1e7d6d;
        }

        .inspira-card-offer .btn {
            margin-top: auto;
            font-weight: 600;
        }

        .inspira-card-market__empty {
            padding: 24px;
            background: #f8f9fa;
            border-radius: 12px;
            color: #6c757d;
        }

        .inspira-card-history__header {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            margin-bottom: 12px;
            gap: 12px;
        }

        .inspira-card-history__title {
            font-size: 20px;
            font-weight: 600;
        }

        @media (max-width: 991px) {
            .inspira-card-panel,
            .inspira-card-offer,
            .inspira-card-history {
                padding: 20px;
            }
        }
    </style>

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
                            <div class="inspira-card-visual__headline">{{ $displayCard?->name ?? trans('plugins/hotel::customer-card.purchase.placeholder_name') }}</div>
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
                                            <span class="inspira-card-offer__badge">-{{ number_format($card->discount_percent, 0) }}%</span>
                                        </div>

                                        <ul class="inspira-card-offer__list">
                                            <li>{{ trans('plugins/hotel::customer-card.purchase.units_included', ['units' => $card->units_total]) }}</li>
                                            <li>{{ trans('plugins/hotel::customer-card.purchase.base_price_each', ['price' => format_price($card->base_price)]) }}</li>
                                            <li>{{ trans('plugins/hotel::customer-card.purchase.discount_note', ['percent' => number_format($card->discount_percent, 0)]) }}</li>
                                            <li>{{ trans('plugins/hotel::customer-card.purchase.valid_until_inline', ['date' => $card->valid_until ? $card->valid_until->translatedFormat('d.m.Y') : trans('plugins/hotel::customer-card.purchase.no_expiry')]) }}</li>
                                        </ul>

                                        <div>
                                            <div class="inspira-card-offer__price">{{ format_price($card->purchase_price ?? 0) }}</div>
                                            <p class="mb-0 text-muted" style="font-size: 12px;">{{ trans('plugins/hotel::customer-card.purchase.price_hint') }}</p>
                                        </div>

                                        <a class="btn btn-primary w-100" href="{{ route('customer.cards.checkout', $card) }}">
                                            {{ trans('plugins/hotel::customer-card.purchase.buy_button') }}
                                        </a>
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
                <p class="text-muted mb-0" style="font-size: 13px;">{{ trans('plugins/hotel::customer-card.purchase.history_subtitle') }}</p>
            </div>

            @if ($usages->isEmpty())
                <p class="mb-0 text-muted">{{ trans('plugins/hotel::customer-card.purchase.history_empty') }}</p>
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

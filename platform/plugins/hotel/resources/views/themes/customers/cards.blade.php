@extends(HotelHelper::viewPath('customers.master'))

@section('content')
    <div class="customer-card mb-4">
        <div class="customer-card-header d-flex justify-content-between align-items-center">
            <div>
                <h2 class="customer-card-title">{{ __('Meine Kundenkarten') }}</h2>
                <p class="customer-card-subtitle">{{ __('Behalte deine aktiven Karten, Restguthaben und Laufzeiten im Blick.') }}</p>
            </div>
            <a class="btn btn-primary" href="#cards-marketplace">{{ __('Neue Karte kaufen') }}</a>
        </div>

        <div class="customer-card-body">
            @if ($cards->isEmpty())
                <div class="alert alert-info mb-0">
                    {{ __('Du besitzt aktuell noch keine Kundenkarte. Sichere dir jetzt deine erste Karte und erhalte exklusive Rabatte auf deine Lieblingskurse.') }}
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>{{ __('Karte') }}</th>
                                <th>{{ __('Rabatt') }}</th>
                                <th>{{ __('Verbleibende Einheiten') }}</th>
                                <th>{{ __('Gültig bis') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cards as $card)
                                @php
                                    $statusColor = $card->status_color === 'warning' ? 'badge-warning' : ($card->status_color === 'danger' ? 'badge-danger' : 'badge-success');
                                    $statusLabel = $card->status_label;
                                @endphp
                                <tr>
                                    <td>{{ $card->name }}</td>
                                    <td>{{ number_format($card->discount_percent, 2) }}%</td>
                                    <td>{{ $card->units_remaining }} / {{ $card->units_total }}</td>
                                    <td>{{ $card->valid_until ? $card->valid_until->translatedFormat('d.m.Y') : __('Ohne Ablauf') }}</td>
                                    <td><span class="badge {{ $statusColor }}">{{ $statusLabel }}</span></td>
                                    <td class="text-end">
                                        @if ($card->units_remaining <= 0 || ($card->valid_until && $card->valid_until->isPast()))
                                            <a class="btn btn-sm btn-outline-primary" href="#cards-marketplace">{{ __('Neue Karte kaufen') }}</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="customer-card mb-4">
        <div class="customer-card-header">
            <h2 class="customer-card-title">{{ __('Verlauf') }}</h2>
            <p class="customer-card-subtitle">{{ __('Hier siehst du, wann und für welche Kurse du deine Kundenkarten genutzt hast.') }}</p>
        </div>

        <div class="customer-card-body">
            @if ($usages->isEmpty())
                <p class="mb-0 text-muted">{{ __('Noch keine Buchungen mit Kundenkarten durchgeführt.') }}</p>
            @else
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th>{{ __('Datum') }}</th>
                                <th>{{ __('Karte') }}</th>
                                <th>{{ __('Kurs') }}</th>
                                <th>{{ __('Verbrauchte Einheiten') }}</th>
                                <th>{{ __('Ersparnis') }}</th>
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

    <div class="customer-card" id="cards-marketplace">
        <div class="customer-card-header">
            <h2 class="customer-card-title">{{ __('Neue Karte kaufen') }}</h2>
            <p class="customer-card-subtitle">{{ __('Wähle das passende Paket und sichere dir deine Vorteile für kommende Buchungen.') }}</p>
        </div>

        <div class="customer-card-body">
            @if ($availableCards->isEmpty())
                <p class="mb-0 text-muted">{{ __('Aktuell stehen keine neuen Karten zur Verfügung. Schau bald wieder vorbei!') }}</p>
            @else
                <div class="row g-3">
                    @foreach ($availableCards as $card)
                        <div class="col-md-6 col-xl-4">
                            <div class="border rounded h-100 p-4 bg-light">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h3 class="h5 mb-1">{{ $card->name }}</h3>
                                        <span class="badge badge-primary">{{ number_format($card->discount_percent, 2) }}% {{ __('Rabatt') }}</span>
                                    </div>
                                    <span class="text-muted">{{ $card->type?->label() }}</span>
                                </div>

                                <ul class="list-unstyled mb-4 text-muted small">
                                    <li>{{ __('Einheiten') }}: <strong>{{ $card->units_total }}</strong></li>
                                    <li>{{ __('Basispreis je Einheit') }}: <strong>{{ format_price($card->base_price) }}</strong></li>
                                    <li>{{ __('Gesamtwert') }}: <strong>{{ format_price($card->base_price * $card->units_total) }}</strong></li>
                                    <li>{{ __('Gültig bis') }}: <strong>{{ $card->valid_until ? $card->valid_until->translatedFormat('d.m.Y') : __('Ohne Ablauf') }}</strong></li>
                                </ul>

                                <a class="btn btn-success w-100" href="{{ url('checkout/card/' . $card->getKey()) }}">
                                    {{ __('Jetzt kaufen') }}
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection

@extends(HotelHelper::viewPath('customers.master'))

@section('content')
    <style>
        .card-success-shell {
            display: flex;
            flex-direction: column;
            gap: 20px;
            max-width: 820px;
            margin: 0 auto;
        }

        .card-success-header {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            padding: 24px;
            text-align: center;
        }

        .card-success-icon {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            margin: 0 auto 14px;
            background: #e5f6f2;
            color: #1e7d6d;
            font-size: 22px;
            font-weight: 700;
        }

        .card-success-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .card-success-subtitle {
            color: #6c757d;
            margin: 0;
            font-size: 15px;
        }

        .inspira-card-preview {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            padding: 22px;
        }

        .inspira-card-preview__header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 16px;
        }

        .inspira-card-preview__title {
            font-size: 18px;
            font-weight: 600;
            margin: 0;
        }

        .inspira-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.4px;
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

        .inspira-card-preview__body {
            background: #f3f8f7;
            border-radius: 14px;
            padding: 18px;
            display: grid;
            gap: 10px;
        }

        .inspira-card-preview__owner,
        .inspira-card-preview__row {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            font-size: 14px;
        }

        .inspira-card-preview__owner {
            font-weight: 600;
            color: #1f2d3d;
        }

        .inspira-card-preview__row span:first-child {
            color: #6c757d;
        }

        .card-success-actions {
            display: flex;
            justify-content: center;
            margin-top: 4px;
        }

        .card-success-actions .btn {
            min-width: 220px;
            padding: 12px 18px;
            border-radius: 999px;
            font-weight: 600;
        }

        @media (max-width: 576px) {
            .card-success-header,
            .inspira-card-preview {
                padding: 18px;
            }

            .card-success-title {
                font-size: 22px;
            }

            .inspira-card-preview__body {
                gap: 12px;
            }

            .card-success-actions .btn {
                width: 100%;
            }
        }
    </style>

    <div class="card-success-shell">
        <div class="card-success-header">
            <div class="card-success-icon">✓</div>
            <h1 class="card-success-title">Karte erfolgreich aktiviert</h1>
            <p class="card-success-subtitle">Deine neue Inspira Karte ist bereit – alle Vorteile sind jetzt freigeschaltet.</p>
        </div>

        <div class="inspira-card-preview">
            <div class="inspira-card-preview__header">
                <h2 class="inspira-card-preview__title">Deine Inspira Karte</h2>
                @php
                    $badgeTone = match ($statusTone) {
                        'warning' => 'warning',
                        'danger' => 'danger',
                        default => 'success',
                    };
                @endphp
                <span class="inspira-badge inspira-badge--{{ $badgeTone }}">{{ $activeCard->status_label }}</span>
            </div>

            <div class="inspira-card-preview__body">
                <div class="inspira-card-preview__owner">
                    <span>{{ $activeCard->name }}</span>
                    <span>{{ $customer->name }}</span>
                </div>
                <div class="inspira-card-preview__row">
                    <span>Aktueller Stand</span>
                    <span>{{ $activeCard->units_remaining }} / {{ $activeCard->units_total }}</span>
                </div>
                <div class="inspira-card-preview__row">
                    <span>Gültig bis</span>
                    <span>
                        {{ $activeCard->valid_until ? $activeCard->valid_until->translatedFormat('d.m.Y') : __('Kein Ablaufdatum') }}
                    </span>
                </div>
            </div>
        </div>

        <div class="card-success-actions">
            <a class="btn btn-primary" href="https://inspira-zentrum.net/de/account/cards">Zum Profil</a>
        </div>
    </div>
@endsection

@extends(HotelHelper::viewPath('customers.master'))

@section('content')
    @php
        $user = auth('customer')->user();

        $details = [
            [
                'label' => __('Name'),
                'value' => $user->name,
            ],
            [
                'label' => __('E-Mail'),
                'value' => $user->email,
            ],
            [
                'label' => __('Geburtsdatum'),
                'value' => $user->dob,
            ],
            [
                'label' => __('Telefon'),
                'value' => $user->phone,
            ],
            [
                'label' => __('Land'),
                'value' => $user->country,
            ],
            [
                'label' => __('Bundesland / Provinz'),
                'value' => $user->state,
            ],
            [
                'label' => __('Stadt'),
                'value' => $user->city,
            ],
            [
                'label' => __('Adresse'),
                'value' => $user->address,
            ],
            [
                'label' => __('Postleitzahl'),
                'value' => $user->zip,
            ],
        ];
    @endphp

    <div class="customer-card">
        <div class="customer-card-header">
            <h2 class="customer-card-title">{{ __('Kontoinformationen') }}</h2>
            <p class="customer-card-subtitle">{{ __('Prüfen Sie Ihre persönlichen Daten und halten Sie sie aktuell.') }}</p>
        </div>

        <div class="customer-card-body">
            <div class="customer-info-grid">
                @foreach ($details as $item)
                    @continue(blank($item['value']))

                    <div class="customer-info-item">
                        <span class="customer-info-label">{{ $item['label'] }}</span>
                        <span class="customer-info-value">{{ $item['value'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

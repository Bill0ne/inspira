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
                'label' => __('Email'),
                'value' => $user->email,
            ],
            [
                'label' => __('Date of birth'),
                'value' => $user->dob,
            ],
            [
                'label' => __('Phone'),
                'value' => $user->phone,
            ],
            [
                'label' => __('Country'),
                'value' => $user->country,
            ],
            [
                'label' => __('State / Province'),
                'value' => $user->state,
            ],
            [
                'label' => __('City'),
                'value' => $user->city,
            ],
            [
                'label' => __('Address'),
                'value' => $user->address,
            ],
            [
                'label' => __('Postal / Zip code'),
                'value' => $user->zip,
            ],
        ];
    @endphp

    <div class="customer-card">
        <div class="customer-card-header">
            <h2 class="customer-card-title">{{ __('Account information') }}</h2>
            <p class="customer-card-subtitle">{{ __('Review and keep your personal information up to date.') }}</p>
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

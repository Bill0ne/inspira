@extends(HotelHelper::viewPath('customers.master'))

@section('content')
    <div class="customer-card">
        <div class="customer-card-header">
            <h2 class="customer-card-title">{{ SeoHelper::getTitle() }}</h2>
            <p class="customer-card-subtitle">{{ __('Here is everything we know about your stay, including invoices and room details.') }}</p>
        </div>

        <div class="customer-card-body customer-card-body--flush">
            @include('plugins/hotel::booking-info', ['route' => 'customer.generate-invoice'])
        </div>
    </div>
@endsection

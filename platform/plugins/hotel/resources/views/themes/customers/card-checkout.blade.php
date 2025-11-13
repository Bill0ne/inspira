@extends(HotelHelper::viewPath('customers.master'))

@section('content')
    @if (is_plugin_active('payment'))
        <link rel="stylesheet" href="{{ asset('vendor/core/plugins/payment/css/payment.css') }}">
        @php
            Theme::asset()->container('header')->usePath()->add('jquery', 'plugins/jquery.min.js');
            Theme::asset()->container('header')->add('payment-js', 'vendor/core/plugins/payment/js/payment.js');
        @endphp
        {!! apply_filters(PAYMENT_FILTER_HEADER_ASSETS, null) !!}
    @endif
    @php
        Theme::asset()
            ->container('footer')
            ->add('hotel-customer-card-js', 'vendor/core/plugins/hotel/js/customer-card.js', ['jquery']);
    @endphp
    <style>
        .card-checkout-wrapper {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .card-checkout-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #578E88;
            font-weight: 500;
            text-decoration: none;
        }

        .card-checkout-panel {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            padding: 28px;
            height: 100%;
        }

        .card-checkout-panel h2,
        .card-checkout-panel h3 {
            font-weight: 600;
        }

        .card-checkout-summary__card {
            background: #f3f8f7;
            border-radius: 18px;
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .card-checkout-summary__name {
            font-size: 22px;
            color: #1e7d6d;
            font-weight: 600;
        }

        .card-checkout-summary__list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 8px;
            font-size: 14px;
            color: #495057;
        }

        .card-checkout-summary__price {
            font-size: 28px;
            font-weight: 600;
            color: #1e7d6d;
        }

        .card-checkout-alert {
            background: #fff4e6;
            border-radius: 12px;
            padding: 14px 18px;
            font-size: 13px;
            color: #c97a00;
            margin-top: 16px;
        }

        @media (max-width: 991px) {
            .card-checkout-panel {
                padding: 22px;
            }
        }
    </style>

    <div class="card-checkout-wrapper">
        <a href="{{ route('customer.cards') }}" class="card-checkout-back">
            <i class="fal fa-arrow-left"></i>
            <span>{{ trans('plugins/hotel::customer-card.checkout.back_link') }}</span>
        </a>

        <div class="row g-4 align-items-stretch">
            <div class="col-lg-7">
                <div class="card-checkout-panel h-100">
                    <h2 class="mb-3">{{ trans('plugins/hotel::customer-card.checkout.title') }}</h2>
                    <p class="text-muted mb-4">{{ trans('plugins/hotel::customer-card.checkout.subtitle', ['name' => $customerCard->name]) }}</p>

                    <div class="card-checkout-summary__card mb-4">
                        <div class="card-checkout-summary__name">
                            {{ $customerCard->name }}
                            @if ($customerCard->uid)
                                <span class="badge bg-success ms-2">{{ $customerCard->uid }}</span>
                            @endif
                        </div>
                        <div>{{ $user->name }}</div>
                        <ul class="card-checkout-summary__list">
                            <li>{{ trans('plugins/hotel::customer-card.purchase.units_included', ['units' => $customerCard->units_total]) }}</li>
                            <li>{{ trans('plugins/hotel::customer-card.purchase.base_price_each', ['price' => format_price($customerCard->base_price)]) }}</li>
                            <li>{{ trans('plugins/hotel::customer-card.purchase.discount_note', ['percent' => number_format($customerCard->discount_percent, 0)]) }}</li>
                            <li>{{ trans('plugins/hotel::customer-card.purchase.valid_until_inline', ['date' => $customerCard->valid_until ? $customerCard->valid_until->translatedFormat('d.m.Y') : trans('plugins/hotel::customer-card.purchase.no_expiry')]) }}</li>
                        </ul>
                    </div>

                    <div>
                        <span class="text-muted">{{ trans('plugins/hotel::customer-card.checkout.total_label') }}</span>
                        <div class="card-checkout-summary__price">{{ format_price($purchasePrice) }}</div>
                        <p class="text-muted mb-0" style="font-size: 13px;">{{ trans('plugins/hotel::customer-card.checkout.price_note') }}</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card-checkout-panel h-100">
                    <h3 class="mb-3">{{ trans('plugins/hotel::customer-card.checkout.confirm_title') }}</h3>
                    <p class="text-muted">{{ trans('plugins/hotel::customer-card.checkout.confirm_description') }}</p>

                    <div class="card-checkout-alert">
                        {{ trans('plugins/hotel::customer-card.checkout.restriction_note') }}
                    </div>

                    <form method="POST" action="{{ route('customer.cards.purchase', $customerCard) }}" class="mt-4 payment-checkout-form">
                        @csrf
                        <input type="hidden" name="amount" value="{{ $purchasePrice }}">
                        @if (isset($order))
                            <input type="hidden" name="order_id" value="{{ $order->getKey() }}">
                            <input type="hidden" name="order_type" value="customer_card">
                        @endif
                        @if (is_plugin_active('payment'))
                            <div class="mb-3">
                                <label class="form-label">{{ __('Zahlungsmethode') }}</label>
                                <ul class="list-group list_payment_method">
                                    {!! PaymentMethods::render() !!}
                                </ul>
                            </div>
                        @endif
                        <button type="submit" class="btn btn-primary w-100 btn-lg">
                            {{ trans('plugins/hotel::customer-card.checkout.submit_button', ['price' => format_price($purchasePrice)]) }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @if (is_plugin_active('payment'))
        {!! apply_filters(PAYMENT_FILTER_FOOTER_ASSETS, null) !!}
    @endif
@endsection

@extends(BaseHelper::getAdminMasterLayoutTemplate())

@push('header')
    <style>
        .empty-img svg {
            --bb-icon-size: 10rem;
        }
    </style>
@endpush

@section('content')
    <x-core::card>
        <div class="page page-center" style="min-height: calc(100vh - 25rem)">
            <div class="container container-tight py-4">
                <div class="empty">
                    <div class="empty-img">
                        <x-core::icon name="ti ti-id" />
                    </div>
                    <p class="empty-title">{{ trans('plugins/hotel::customer-card.intro.title') }}</p>
                    <p class="empty-subtitle text-secondary">
                        {{ trans('plugins/hotel::customer-card.intro.description') }}
                    </p>
                    <div class="empty-action">
                        <x-core::button
                            color="primary"
                            tag="a"
                            :href="route('customer-cards.create')"
                        >
                            {{ trans('plugins/hotel::customer-card.intro.button_text') }}
                        </x-core::button>
                    </div>
                </div>
            </div>
        </div>
    </x-core::card>
@endsection

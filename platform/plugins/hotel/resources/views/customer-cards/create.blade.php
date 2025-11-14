@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    @php($templateCollection = ($templates ?? collect()))

    <x-core::form :url="route('customer-cards.store')" method="post" class="customer-card-form">
        @if($templateCollection->isNotEmpty())
            @include('plugins/hotel::customer-cards.assign', [
                'templates' => $templateCollection,
                'customers' => $customers,
                'card' => $card,
            ])
        @else
            <div class="alert alert-warning">
                <strong>{{ trans('plugins/hotel::customer-card.form.assignment.empty_title') }}</strong>
                <div class="mt-2 mb-0">{{ trans('plugins/hotel::customer-card.form.assignment.empty_description') }}</div>
            </div>

            <a class="btn btn-primary" href="{{ route('customer-cards.templates') }}">
                {{ trans('plugins/hotel::customer-card.form.assignment.empty_action') }}
            </a>

            @include('plugins/hotel::customer-cards.partials.scripts', compact('jsValidator'))
        @endif
    </x-core::form>
@endsection

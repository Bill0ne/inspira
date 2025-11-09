@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <x-core::form :url="route('customer-cards.edit', $card)" method="put" class="customer-card-form">
        @include('plugins/hotel::customer-cards.partials.form', ['card' => $card])
    </x-core::form>
@endsection

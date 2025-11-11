@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <x-core::form :url="route('customer-cards.update', $card)" method="put" class="customer-card-form">
        @include('plugins/hotel::customer-cards.form')
    </x-core::form>
@endsection

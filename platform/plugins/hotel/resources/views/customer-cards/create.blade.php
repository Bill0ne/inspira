@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <x-core::form :url="route('customer-cards.store')" method="post" class="customer-card-form">
        @include('plugins/hotel::customer-cards.form')
    </x-core::form>
@endsection

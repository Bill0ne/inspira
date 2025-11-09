@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <x-core::form :url="route('customer-cards.create')" method="post" class="customer-card-form">
        @include('plugins/hotel::customer-cards.partials.form')
    </x-core::form>
@endsection

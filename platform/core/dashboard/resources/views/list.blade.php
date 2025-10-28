@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="row">
        <div class="col-12">
            {!! apply_filters(DASHBOARD_FILTER_ADMIN_NOTIFICATIONS, null) !!}
        </div>

        <div class="col-12">
            <div class="row row-cards">
                @foreach ($statWidgets as $widget)
                    {!! $widget !!}
                @endforeach
            </div>
        </div>
    </div>

    <div class="mb-3 col-12">
        {!! apply_filters(DASHBOARD_FILTER_TOP_BLOCKS, null) !!}
    </div>

    <div class="col-12">
        <div
            id="list_widgets"
            class="row row-cards"
            data-bb-toggle="widgets-list"
            data-url="{{ route('dashboard.update_widget_order') }}"
        >
            @foreach ($userWidgets as $widget)
                {!! $widget !!}
            @endforeach
        </div>
    </div>
@endsection

@push('footer')
    @include('core/dashboard::partials.modals', compact('widgets'))
@endpush

@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="widget meta-boxes">
                <div class="widget-title justify-content-between">
                    <h4>
                        <span>{{ trans('plugins/hotel::ical.sync_calendars') }}</span>
                    </h4>
                    <div class="widget-action">
                        <a href="{{ route('ical.documentation') }}" class="btn btn-secondary">
                            <x-core::icon name="ti ti-book" class="me-1" /> {{ trans('plugins/hotel::ical.documentation') }}
                        </a>
                        <a href="{{ route('ical.logs.index') }}" class="btn btn-secondary">
                            <x-core::icon name="ti ti-history" class="me-1" /> {{ trans('plugins/hotel::ical.logs') }}
                        </a>
                        <button class="btn btn-info sync-all-calendars">
                            <x-core::icon name="ti ti-refresh" class="me-1" /> {{ trans('plugins/hotel::ical.sync_all_calendars') }}
                        </button>
                    </div>
                </div>
                <div class="widget-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>{{ trans('core/base::tables.name') }}</th>
                                    <th>{{ trans('plugins/hotel::ical.external_calendars') }}</th>
                                    <th>{{ trans('plugins/hotel::ical.last_sync') }}</th>
                                    <th class="text-end">{{ trans('core/base::tables.operations') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rooms as $room)
                                    <tr>
                                        <td>{{ $room->name }}</td>
                                        <td>{{ $room->calendars->count() }}</td>
                                        <td>
                                            @if($room->calendars->isNotEmpty())
                                                {{ $room->calendars->max('last_synced_at') ? $room->calendars->max('last_synced_at')->diffForHumans() : trans('plugins/hotel::ical.never') }}
                                            @else
                                                {{ trans('plugins/hotel::ical.never') }}
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group">
                                                <a href="{{ route('ical.edit', $room->id) }}" class="btn btn-sm btn-info">
                                                    <x-core::icon name="ti ti-edit" class="me-1" /> {{ trans('core/base::tables.edit') }}
                                                </a>
                                                <a href="{{ route('ical.download', $room->id) }}" class="btn btn-sm btn-primary">
                                                    <x-core::icon name="ti ti-download" class="me-1" /> {{ trans('plugins/hotel::ical.download') }}
                                                </a>
                                                @if($room->calendars->isNotEmpty())
                                                    <button class="btn btn-sm btn-warning sync-calendar" data-room-id="{{ $room->id }}">
                                                        <x-core::icon name="ti ti-refresh" class="me-1" /> {{ trans('plugins/hotel::ical.sync') }}
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

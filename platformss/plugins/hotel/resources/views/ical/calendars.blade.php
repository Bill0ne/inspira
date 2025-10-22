<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>{{ trans('core/base::tables.name') }}</th>
                <th>{{ trans('plugins/hotel::ical.calendar_url') }}</th>
                <th>{{ trans('plugins/hotel::ical.last_sync') }}</th>
                <th class="text-end">{{ trans('core/base::tables.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($calendars as $calendar)
                <tr>
                    <td>{{ $calendar->name }}</td>
                    <td>
                        <div class="input-group">
                            <input type="text" class="form-control" value="{{ $calendar->url }}" readonly>
                            <div class="input-group-append">
                                <x-core::button
                                    class="btn-outline-secondary"
                                    data-bb-toggle="clipboard"
                                    data-clipboard-text="{{ $calendar->url }}"
                                    data-clipboard-message="{{ trans('core/base::base.copied') }}"
                                >
                                    <x-core::icon name="ti ti-clipboard" data-clipboard-icon="true" />
                                    <x-core::icon name="ti ti-check" data-clipboard-success-icon="true" class="text-success d-none" />
                                </x-core::button>
                            </div>
                        </div>
                    </td>
                    <td>{{ $calendar->last_synced_at ? $calendar->last_synced_at->diffForHumans() : trans('plugins/hotel::ical.never') }}</td>
                    <td class="text-end">
                        <a href="#" class="btn btn-sm btn-icon btn-danger delete-calendar" data-url="{{ route('ical.destroy', $calendar->id) }}" data-bs-toggle="tooltip" title="{{ trans('core/base::tables.delete') }}">
                            <x-core::icon name="ti ti-trash" />
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

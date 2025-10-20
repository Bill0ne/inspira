<div class="form-group">
    <label>{{ trans('plugins/hotel::ical.export_url') }}</label>
    <div class="input-group">
        <input type="text" class="form-control" value="{{ route('public.ical', $room->slug) }}" readonly>
        <div class="input-group-append">
            <x-core::button
                class="btn-outline-secondary"
                data-bb-toggle="clipboard"
                data-clipboard-text="{{ route('public.ical', $room->slug) }}"
                data-clipboard-message="{{ trans('core/base::base.copied') }}"
            >
                <x-core::icon name="ti ti-clipboard" data-clipboard-icon="true" />
                <x-core::icon name="ti ti-check" data-clipboard-success-icon="true" class="text-success d-none" />
            </x-core::button>
        </div>
    </div>
    <small class="form-text text-muted">
        {{ trans('plugins/hotel::ical.export_description') }}
    </small>
</div>

<div class="form-group">
    <a href="{{ route('ical.download', $room->id) }}" class="btn btn-primary">
        <x-core::icon name="ti ti-download" class="me-1" /> {{ trans('plugins/hotel::ical.download_ical_file') }}
    </a>
</div>

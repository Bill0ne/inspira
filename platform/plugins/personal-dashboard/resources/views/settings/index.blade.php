@extends('core/base::layouts.master')

@php use Illuminate\Support\Arr; @endphp

@section('content')
    <div class="row gy-4">
        <div class="col-12 col-xl-6">
            <div class="card h-100">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        {{ trans('plugins/personal-dashboard::settings.general.heading') }}
                    </h4>
                </div>

                <div class="card-body">
                    <form action="{{ route('personal-dashboard.settings.general') }}" method="post">
                        @csrf

                        <div class="form-check form-switch mb-4">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                id="allow_user_overrides"
                                name="allow_user_overrides"
                                value="1"
                                @checked(Arr::get($settings, 'allow_user_overrides', true))
                            >
                            <label class="form-check-label" for="allow_user_overrides">
                                {{ trans('plugins/personal-dashboard::settings.general.allow_user_overrides') }}
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            {{ trans('plugins/personal-dashboard::settings.general.save_button') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0">
                            {{ trans('plugins/personal-dashboard::settings.custom_widgets.heading') }}
                        </h4>
                        <p class="text-muted mb-0 small">
                            {{ trans('plugins/personal-dashboard::settings.custom_widgets.description') }}
                        </p>
                    </div>
                    <a class="btn btn-primary" href="{{ route('personal-dashboard.settings.custom-widgets.create') }}">
                        <i class="ti ti-plus"></i>
                        {{ trans('plugins/personal-dashboard::settings.custom_widgets.create_button') }}
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>{{ trans('plugins/personal-dashboard::settings.custom_widgets.table.name') }}</th>
                                <th>{{ trans('plugins/personal-dashboard::settings.custom_widgets.table.key') }}</th>
                                <th class="text-end">{{ trans('plugins/personal-dashboard::settings.custom_widgets.table.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customWidgets as $widget)
                                <tr>
                                    <td>
                                        <strong>{{ $widget->getTitle() }}</strong>
                                        <div class="text-muted small">{{ $widget->getDescription() }}</div>
                                    </td>
                                    <td><code>{{ $widget->key }}</code></td>
                                    <td class="text-end">
                                        <a class="btn btn-sm btn-outline-primary" href="{{ route('personal-dashboard.settings.custom-widgets.edit', $widget) }}">
                                            <i class="ti ti-edit"></i>
                                            {{ trans('core/base::forms.edit') }}
                                        </a>
                                        <form
                                            class="d-inline"
                                            method="post"
                                            action="{{ route('personal-dashboard.settings.custom-widgets.destroy', $widget) }}"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-outline-danger ms-2"
                                                onclick="return confirm('{{ trans('core/base::tables.confirm_delete_msg') }}');"
                                            >
                                                <i class="ti ti-trash"></i>
                                                {{ trans('core/base::forms.delete') }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">
                                        {{ trans('plugins/personal-dashboard::settings.custom_widgets.empty') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        {{ trans('plugins/personal-dashboard::settings.widgets.heading') }}
                    </h4>
                    <p class="text-muted mb-0 small">
                        {{ trans('plugins/personal-dashboard::settings.widgets.description') }}
                    </p>
                </div>
                <div class="card-body">
                    <form action="{{ route('personal-dashboard.settings.widgets') }}" method="post">
                        @csrf

                        <div class="table-responsive">
                            <table class="table table-borderless align-middle">
                                <thead>
                                    <tr>
                                        <th>{{ trans('plugins/personal-dashboard::settings.widgets.table.name') }}</th>
                                        <th>{{ trans('plugins/personal-dashboard::settings.widgets.table.key') }}</th>
                                        <th>{{ trans('plugins/personal-dashboard::settings.widgets.table.order') }}</th>
                                        <th>{{ trans('plugins/personal-dashboard::settings.widgets.table.enabled') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($settings['widgets'] as $key => $config)
                                        <tr>
                                            <td>
                                                <input type="hidden" name="widgets[{{ $key }}][original_key]" value="{{ $key }}">
                                                <input
                                                    type="text"
                                                    name="widgets[{{ $key }}][label]"
                                                    value="{{ Arr::get($config, 'label', $key) }}"
                                                    class="form-control"
                                                >
                                            </td>
                                            <td><code>{{ $key }}</code></td>
                                            <td style="max-width: 120px;">
                                                <input
                                                    type="number"
                                                    name="widgets[{{ $key }}][order]"
                                                    value="{{ Arr::get($config, 'order', 0) }}"
                                                    class="form-control"
                                                >
                                            </td>
                                            <td>
                                                <div class="form-check form-switch">
                                                    <input
                                                        class="form-check-input"
                                                        type="checkbox"
                                                        name="widgets[{{ $key }}][enabled]"
                                                        value="1"
                                                        @checked(Arr::get($config, 'enabled', true))
                                                    >
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            {{ trans('plugins/personal-dashboard::settings.widgets.save_button') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

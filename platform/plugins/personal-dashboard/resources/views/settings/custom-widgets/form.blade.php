@extends('core/base::layouts.master')

@php
    use Illuminate\Support\Arr;

    $colors = config('plugins.personal-dashboard.general.colors', []);
    $icons = config('plugins.personal-dashboard.general.icons', []);
    $columns = config('plugins.personal-dashboard.general.columns', []);
@endphp

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        {{ $widget->exists ? trans('plugins/personal-dashboard::settings.custom_widgets.edit_title') : trans('plugins/personal-dashboard::settings.custom_widgets.create_title') }}
                    </h4>
                    <a class="btn btn-outline-secondary" href="{{ route('personal-dashboard.settings.index') }}">
                        <i class="ti ti-arrow-left"></i>
                        {{ trans('core/base::forms.back') }}
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ $submitRoute }}" method="post">
                        @csrf
                        @if ($widget->exists)
                            @method('PUT')
                        @endif

                        <div class="row g-4">
                            <div class="col-12 col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label" for="widget-key">
                                        {{ trans('plugins/personal-dashboard::settings.custom_widgets.fields.key') }}
                                    </label>
                                    <input
                                        id="widget-key"
                                        type="text"
                                        name="key"
                                        value="{{ old('key', $widget->key) }}"
                                        class="form-control @error('key') is-invalid @enderror"
                                        @if($widget->exists) readonly @endif
                                    >
                                    @error('key')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-lg-3">
                                <div class="mb-3">
                                    <label class="form-label" for="widget-icon">
                                        {{ trans('plugins/personal-dashboard::settings.custom_widgets.fields.icon') }}
                                    </label>
                                    <select class="form-select" id="widget-icon" name="icon">
                                        <option value="">
                                            {{ trans('core/base::forms.select_placeholder') }}
                                        </option>
                                        @foreach($icons as $value => $label)
                                            <option value="{{ $value }}" @selected(old('icon', $widget->icon) === $value)>
                                                {{ $label }} ({{ $value }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-lg-3">
                                <div class="mb-3">
                                    <label class="form-label" for="widget-color">
                                        {{ trans('plugins/personal-dashboard::settings.custom_widgets.fields.color') }}
                                    </label>
                                    <select class="form-select" id="widget-color" name="color">
                                        <option value="">
                                            {{ trans('core/base::forms.select_placeholder') }}
                                        </option>
                                        @foreach($colors as $value => $label)
                                            <option value="{{ $value }}" @selected(old('color', $widget->color) === $value)>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-lg-4">
                                <div class="mb-3">
                                    <label class="form-label" for="widget-column">
                                        {{ trans('plugins/personal-dashboard::settings.custom_widgets.fields.column_class') }}
                                    </label>
                                    <select class="form-select" id="widget-column" name="column_class">
                                        <option value="">
                                            {{ trans('core/base::forms.select_placeholder') }}
                                        </option>
                                        @foreach($columns as $value => $label)
                                            <option value="{{ $value }}" @selected(old('column_class', $widget->column_class) === $value)>
                                                {{ $label }} ({{ $value }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-lg-4">
                                <div class="mb-3">
                                    <label class="form-label" for="widget-view">
                                        {{ trans('plugins/personal-dashboard::settings.custom_widgets.fields.view_path') }}
                                    </label>
                                    <input
                                        id="widget-view"
                                        type="text"
                                        name="view_path"
                                        value="{{ old('view_path', $widget->view_path) }}"
                                        class="form-control"
                                    >
                                </div>
                            </div>
                            <div class="col-12 col-lg-4">
                                <div class="mb-3">
                                    <label class="form-label" for="widget-handler">
                                        {{ trans('plugins/personal-dashboard::settings.custom_widgets.fields.handler') }}
                                    </label>
                                    <input
                                        id="widget-handler"
                                        type="text"
                                        name="handler"
                                        value="{{ old('handler', $widget->handler) }}"
                                        class="form-control"
                                    >
                                </div>
                            </div>
                            <div class="col-12 col-lg-4">
                                <div class="mb-3">
                                    <label class="form-label" for="widget-ajax-route">
                                        {{ trans('plugins/personal-dashboard::settings.custom_widgets.fields.ajax_route') }}
                                    </label>
                                    <input
                                        id="widget-ajax-route"
                                        type="text"
                                        name="ajax_route"
                                        value="{{ old('ajax_route', $widget->ajax_route) }}"
                                        class="form-control"
                                    >
                                </div>
                            </div>
                            <div class="col-12 col-lg-4">
                                <div class="mb-3">
                                    <label class="form-label" for="widget-order">
                                        {{ trans('plugins/personal-dashboard::settings.custom_widgets.fields.sort_order') }}
                                    </label>
                                    <input
                                        id="widget-order"
                                        type="number"
                                        name="sort_order"
                                        value="{{ old('sort_order', $widget->sort_order) }}"
                                        class="form-control"
                                    >
                                </div>
                            </div>
                            <div class="col-12 col-lg-4">
                                <div class="form-check form-switch mt-4 pt-2">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="widget-active"
                                        name="is_active"
                                        value="1"
                                        @checked(old('is_active', $widget->is_active))
                                    >
                                    <label class="form-check-label" for="widget-active">
                                        {{ trans('plugins/personal-dashboard::settings.custom_widgets.fields.is_active') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-12 col-lg-4">
                                <div class="form-check form-switch mt-4 pt-2">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="widget-load-callback"
                                        name="has_load_callback"
                                        value="1"
                                        @checked(old('has_load_callback', $widget->has_load_callback))
                                    >
                                    <label class="form-check-label" for="widget-load-callback">
                                        {{ trans('plugins/personal-dashboard::settings.custom_widgets.fields.has_load_callback') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row g-4">
                            @foreach($locales as $locale => $localeName)
                                <div class="col-12 col-lg-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="title-{{ $locale }}">
                                            {{ trans('plugins/personal-dashboard::settings.custom_widgets.fields.title') }} ({{ $localeName }})
                                        </label>
                                        <input
                                            id="title-{{ $locale }}"
                                            type="text"
                                            name="title[{{ $locale }}]"
                                            value="{{ old("title.{$locale}", Arr::get($widget->title, $locale)) }}"
                                            class="form-control @error("title.{$locale}") is-invalid @enderror"
                                        >
                                        @error("title.{$locale}")
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="description-{{ $locale }}">
                                            {{ trans('plugins/personal-dashboard::settings.custom_widgets.fields.description') }} ({{ $localeName }})
                                        </label>
                                        <textarea
                                            id="description-{{ $locale }}"
                                            name="description[{{ $locale }}]"
                                            class="form-control"
                                            rows="4"
                                        >{{ old("description.{$locale}", Arr::get($widget->description, $locale)) }}</textarea>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <hr class="my-4">

                        <div class="mb-3">
                            <label class="form-label" for="widget-settings">
                                {{ trans('plugins/personal-dashboard::settings.custom_widgets.fields.settings') }}
                            </label>
                            <textarea
                                id="widget-settings"
                                name="settings_json"
                                class="form-control"
                                rows="6"
                                placeholder="{{ trans('plugins/personal-dashboard::settings.custom_widgets.fields.settings_placeholder') }}"
                            >{{ old('settings_json', $widget->settings ? json_encode($widget->settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '') }}</textarea>
                            <div class="form-text">
                                {{ trans('plugins/personal-dashboard::settings.custom_widgets.fields.settings_hint') }}
                            </div>
                            @error('settings_json')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            {{ $widget->exists ? trans('core/base::forms.save_changes') : trans('core/base::forms.create') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@props([
    'id' => null,
    'label' => null,
    'labelSrOnly' => false,
    'name' => null,
    'value' => old($name),
    'wrapperClass' => null,
    'wrapperClassDefault' => 'mb-3 position-relative',
    'helperText' => null,
    'labelDescription' => null,
    'errorKey' => $name,
    'required' => false,
    'allowClear' => true,
])

@php
    $id ??= $name ?? Str::random(8);

    $wrapperClass = Arr::toCssClasses([$wrapperClass]);

    $attributes = $attributes->merge([
        'name' => $name,
        'id' => $id,
        'value' => $value,
        'placeholder' => $attributes->get('placeholder', BaseHelper::getDateFormat()),
        'data-date-format' => $attributes->get('data-date-format', BaseHelper::getDateFormat()),
        'data-input' => $attributes->get('data-input', ''),
    ]);

    if (! $attributes->has('readonly')) {
        $attributes = $attributes->merge(['readonly' => 'readonly']);
    }

    $dataOptions = $attributes->get('data-options');

    if (is_array($dataOptions)) {
        $attributes = $attributes->except('data-options')->merge([
            'data-options' => json_encode($dataOptions),
        ]);
    }

    $attributes = $attributes->class([
        'form-control',
        'is-invalid' => $errors->has($errorKey),
    ]);

    if (App::getLocale() !== 'en') {
        Assets::addScriptsDirectly('https://npmcdn.com/flatpickr@4.6.13/dist/l10n/index.js');
    }
@endphp

<x-core::form-group :class="$wrapperClass" :default-class="$wrapperClassDefault">
    @if ($label)
        <x-core::form.label
            :label="$label"
            :for="$id"
            :description="$labelDescription"
            @class(['required' => $required, 'sr-only' => $labelSrOnly])
        />
    @endif

    <div class="input-group datepicker">
        {!! Form::text($name, $value, $attributes->getAttributes()) !!}

        <x-core::button data-toggle icon="ti ti-calendar" :icon-only="true" />

        @if ($allowClear)
            <x-core::button data-clear icon="ti ti-x" :icon-only="true" class="text-danger" />
        @endif
    </div>

    @if ($helperText)
        <x-core::form.helper-text>{!! $helperText !!}</x-core::form.helper-text>
    @endif

    <x-core::form.error :key="$errorKey" />

    {{ $slot }}
</x-core::form-group>

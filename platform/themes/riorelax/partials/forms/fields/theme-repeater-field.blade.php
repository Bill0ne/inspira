@php
    $values = array_values(is_array($options['value']) ? $options['value'] : (array) json_decode($options['value'] ?: '[]', true));
    $fields = $options['fields'];
    $repeaterId = 'repeater_field_' . md5($name) . uniqid('_');
    $added = [];

    if (!empty($values)) {
        foreach ($values as $index => $groupValues) {
            $group = '';
            foreach ($fields as $key => $field) {
                $group .= view('core/base::forms.partials.repeater-item', compact('name', 'index', 'key', 'field', 'values'))->render();
            }
            $added[] = view('core/base::forms.partials.repeater-group', compact('group'))->render();
        }
    }

    $group = '';
    foreach ($fields as $key => $field) {
        $group .= view('core/base::forms.partials.repeater-item', [
            'name' => $name,
            'index' => '__key__',
            'key' => $key,
            'field' => $field,
            'values' => [],
        ])->render();
    }

    $defaultFields = [view('core/base::forms.partials.repeater-group', compact('group'))->render()];
@endphp

<input name="{{ $name }}" type="hidden" value="[]">

<div class="repeater-group" id="{{ $repeaterId }}_group" data-next-index="{{ count($added) }}">
    @foreach ($added as $field)
        {!! $field !!}
    @endforeach
</div>

<div class="mt-3">
    <x-core::button data-target="repeater-add" data-id="{{ $repeaterId }}" type="button">
        {{ __('Add new slot') }}
    </x-core::button>
</div>

@push('footer')
    @include('core/base::forms.partials.repeater-template', compact('repeaterId', 'defaultFields'))
@endpush

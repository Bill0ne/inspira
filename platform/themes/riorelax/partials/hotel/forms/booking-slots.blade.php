@php
    $widgetId = $widgetId ?? uniqid('booking-widget-');
    $slots = $slots ?? [];
    $showHint = $showHint ?? true;
    $addSlotLabel = $addSlotLabel ?? __('Add Slot');
    $minDuration = $minDuration ?? 30;
@endphp

<div class="booking-widget__body">
    <div class="booking-widget__slots" data-slot-list>
        @foreach ($slots as $index => $slot)
            @php
                $slotDate = $slot['date'] ?? '';
                $slotStart = $slot['start'] ?? '';
                $slotEnd = $slot['end'] ?? '';
                $rawValue = $slot['raw'] ?? '';

                $dateId = sprintf('%s-slot-%d-date', $widgetId, $index);
                $startId = sprintf('%s-slot-%d-start', $widgetId, $index);
                $endId = sprintf('%s-slot-%d-end', $widgetId, $index);
            @endphp

            <div class="slot-card" data-slot-card data-index="{{ $index }}">
                <div class="slot-card__field">
                    <label class="slot-card__label" for="{{ $dateId }}" data-slot-label="date">{{ __('Date') }}</label>
                    <input
                        type="text"
                        id="{{ $dateId }}"
                        class="slot-card__input slot-date"
                        data-role="slot-date"
                        data-slot-input="date"
                        placeholder="{{ __('Select date') }}"
                        value="{{ $slotDate }}"
                        autocomplete="off"
                    >
                </div>
                <div class="slot-card__field">
                    <label class="slot-card__label" for="{{ $startId }}" data-slot-label="start">{{ __('Start') }}</label>
                    <input
                        type="text"
                        id="{{ $startId }}"
                        class="slot-card__input slot-start"
                        data-role="slot-start"
                        data-slot-input="start"
                        placeholder="{{ __('Start time') }}"
                        value="{{ $slotStart }}"
                        autocomplete="off"
                    >
                </div>
                <div class="slot-card__field">
                    <label class="slot-card__label" for="{{ $endId }}" data-slot-label="end">{{ __('End') }}</label>
                    <input
                        type="text"
                        id="{{ $endId }}"
                        class="slot-card__input slot-end"
                        data-role="slot-end"
                        data-slot-input="end"
                        placeholder="{{ __('End time') }}"
                        value="{{ $slotEnd }}"
                        autocomplete="off"
                    >
                </div>

                <input type="hidden" name="slots[]" value="{{ $rawValue }}" class="slot-card__value" data-slot-value>

                <button type="button" class="slot-card__remove" data-remove-slot aria-label="{{ __('Remove slot') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endforeach
    </div>

    <button type="button" class="booking-widget__add" data-add-slot>
        <i class="fal fa-plus" aria-hidden="true"></i>
        <span>{{ $addSlotLabel }}</span>
    </button>

    @if ($showHint)
        <p class="booking-widget__hint">{{ __('Each slot must be at least :minutes minutes.', ['minutes' => $minDuration]) }}</p>
    @endif

    <div class="booking-widget__error" data-error role="alert" hidden tabindex="-1"></div>

    <template data-slot-template>
        <div class="slot-card" data-slot-card>
            <div class="slot-card__field">
                <label class="slot-card__label" data-slot-label="date">{{ __('Date') }}</label>
                <input
                    type="text"
                    class="slot-card__input slot-date"
                    data-role="slot-date"
                    data-slot-input="date"
                    placeholder="{{ __('Select date') }}"
                    autocomplete="off"
                >
            </div>
            <div class="slot-card__field">
                <label class="slot-card__label" data-slot-label="start">{{ __('Start') }}</label>
                <input
                    type="text"
                    class="slot-card__input slot-start"
                    data-role="slot-start"
                    data-slot-input="start"
                    placeholder="{{ __('Start time') }}"
                    autocomplete="off"
                >
            </div>
            <div class="slot-card__field">
                <label class="slot-card__label" data-slot-label="end">{{ __('End') }}</label>
                <input
                    type="text"
                    class="slot-card__input slot-end"
                    data-role="slot-end"
                    data-slot-input="end"
                    placeholder="{{ __('End time') }}"
                    autocomplete="off"
                >
            </div>

            <input type="hidden" name="slots[]" value="" class="slot-card__value" data-slot-value>

            <button type="button" class="slot-card__remove" data-remove-slot aria-label="{{ __('Remove slot') }}">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </template>
</div>

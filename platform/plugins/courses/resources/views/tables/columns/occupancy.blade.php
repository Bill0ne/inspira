@php
    $chairs = (int) ($chairs ?? 5);
    $filledChairs = (int) min(max($filledChairs ?? 0, 0), $chairs);
@endphp

<div class="d-flex flex-column gap-2">
    <div class="d-flex align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center flex-wrap gap-2" style="max-width: 200px;">
            @for ($index = 0; $index < $chairs; $index++)
                @php($filled = $index < $filledChairs)
                <span class="d-inline-flex align-items-center justify-content-center" style="width: 28px;height: 28px;border-radius: 10px;border: 1px solid {{ $filled ? '#1F5A53' : '#E1EBE8' }};background: {{ $filled ? 'linear-gradient(135deg,#2F6A62 0%,#4FA398 100%)' : '#FFFFFF' }};box-shadow: 0 3px 6px rgba(21,66,59,0.18);">
                    <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" width="18" height="18">
                        <rect x="6" y="8" width="20" height="10" rx="3" fill="{{ $filled ? '#ffffff' : '#CCE1DC' }}" opacity="{{ $filled ? '1' : '0.85' }}" />
                        <rect x="8" y="18" width="16" height="6" rx="2" fill="{{ $filled ? '#ffffff' : '#CCE1DC' }}" opacity="{{ $filled ? '1' : '0.85' }}" />
                        <rect x="6" y="24" width="6" height="4" rx="1.5" fill="{{ $filled ? '#CCE1DC' : '#E7F0EE' }}" />
                        <rect x="20" y="24" width="6" height="4" rx="1.5" fill="{{ $filled ? '#CCE1DC' : '#E7F0EE' }}" />
                    </svg>
                </span>
            @endfor
        </div>
        <span class="px-3 py-1 rounded-pill fw-semibold" style="background: #EEF5F4;color: #1F2A2A;">
            {{ $capacityLabel }}
        </span>
    </div>

    <div style="height: 8px;border-radius: 999px;background: #E1EBE8;overflow: hidden;">
        <span style="display: block;height: 100%;width: {{ $progress }}%;background: linear-gradient(90deg,#2F6A62 0%,#51A198 100%);"></span>
    </div>

    <div class="text-muted small">
        {{ $label }}
    </div>
</div>

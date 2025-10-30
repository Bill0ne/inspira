<div class="d-flex align-items-center gap-3">
    <span class="d-inline-flex align-items-center justify-content-center" style="width: 48px;height: 48px;border-radius: 16px;background: linear-gradient(135deg,#2F6A62 0%,#4FA398 100%);color: #fff;">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none">
            <rect x="3" y="5" width="18" height="16" rx="2" fill="rgba(255,255,255,0.18)" stroke="currentColor" stroke-width="1.2" />
            <path d="M3 9H21" stroke="currentColor" stroke-width="1.2" />
            <path d="M8 3V7" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" />
            <path d="M16 3V7" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" />
        </svg>
    </span>

    <div class="d-flex flex-column">
        <span class="fw-semibold" style="color:#1F2A2A;">{{ $dateLabel }}</span>
        @if ($weekdayLabel)
            <span class="text-muted small">{{ $weekdayLabel }}</span>
        @endif
    </div>

    <div class="ms-auto">
        {!! $timeBadge !!}
    </div>
</div>

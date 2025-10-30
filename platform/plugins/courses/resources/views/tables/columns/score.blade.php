<div class="d-flex align-items-center gap-3">
    <span class="d-inline-flex align-items-center justify-content-center position-relative" title="{{ $ratingLabel }}" style="width: 58px;height: 58px;border-radius: 50%;background: conic-gradient({{ $ringColor }} {{ $sweep }}%, #E7F1EF {{ $sweep }}%);">
        <span class="d-inline-flex align-items-center justify-content-center" style="width: 42px;height: 42px;border-radius: 50%;background: {{ $background }};color: {{ $textColor }};font-weight: 700;box-shadow: 0 6px 12px rgba(18,66,59,0.2);">
            {{ $score }}
        </span>
    </span>
    <div class="d-flex flex-column">
        <span class="fw-semibold" style="color:#1F2A2A;">{{ $ratingLabel }}</span>
        <span class="text-muted small">{{ $subtitle }}</span>
    </div>
</div>

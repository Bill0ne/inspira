@php
    $heading = $userName
        ? trans('plugins/modern::widgets.greeting_heading', ['name' => $userName])
        : trans('plugins/modern::widgets.greeting_heading_guest');
@endphp

<div class="modern-greeting-card">
    <div class="modern-greeting-icon" aria-hidden="true">👋</div>
    <div class="modern-greeting-content">
        <h4 class="modern-greeting-title">{{ $heading }}</h4>
        <p class="modern-greeting-subtitle">{{ trans('plugins/modern::widgets.greeting_subtitle') }}</p>
    </div>
</div>

@push('footer')
    <style>
        .widget-item.modern-greeting-widget .card-body {
            padding: 0;
            background: transparent;
        }

        .modern-greeting-card {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.5rem 1.75rem;
            background: linear-gradient(135deg, #f9fafb, #f1f5f9);
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            font-family: "Inter", "Helvetica Neue", Arial, sans-serif;
        }

        .modern-greeting-icon {
            font-size: 2rem;
            line-height: 1;
        }

        .modern-greeting-title {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
            color: #1f2937;
        }

        .modern-greeting-subtitle {
            margin: 0.25rem 0 0;
            color: #4b5563;
            font-size: 0.95rem;
        }
    </style>
@endpush

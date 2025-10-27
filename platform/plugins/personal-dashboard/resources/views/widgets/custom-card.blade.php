@php use Illuminate\Support\Arr; @endphp

<div class="p-4 h-100 d-flex flex-column justify-content-between">
    <div class="d-flex align-items-center mb-3">
        @if ($widget->icon)
            <span class="badge bg-{{ $widget->color ?: 'primary' }} rounded-pill me-3">
                <i class="{{ $widget->icon }}"></i>
            </span>
        @endif

        <div>
            <h5 class="mb-1 fw-semibold">
                {{ $widget->getTitle() }}
            </h5>
            @if ($widget->getDescription())
                <p class="mb-0 text-muted small">
                    {{ $widget->getDescription() }}
                </p>
            @endif
        </div>
    </div>

    @if (! empty($data['metrics']) && is_array($data['metrics']))
        <div class="row g-3 mb-3">
            @foreach($data['metrics'] as $metric)
                <div class="col-6">
                    <div class="border rounded-3 p-3 h-100">
                        <div class="text-muted small text-uppercase fw-semibold">
                            {{ Arr::get($metric, 'label') }}
                        </div>
                        <div class="fs-4 fw-bold">
                            {{ Arr::get($metric, 'value') }}
                        </div>
                        @if ($difference = Arr::get($metric, 'difference'))
                            <div class="text-muted small">{{ $difference }}</div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if (! empty($data['chart']))
        <div class="chart-container" data-chart='@json($data['chart'])'></div>
    @endif

    @if (! empty($data['footer']))
        <div class="mt-auto pt-3 border-top small text-muted">
            {!! $data['footer'] !!}
        </div>
    @endif
</div>

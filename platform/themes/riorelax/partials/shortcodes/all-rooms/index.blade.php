<section class="services-area pt-20 pb-40">
    @if ($rooms->isNotEmpty())
        <div class="row g-4">
            @foreach ($rooms as $room)
                <div class="col-lg-4 col-md-6 col-sm-12">
                    {!! Theme::partial('rooms.item', compact('room', 'startDate', 'endDate', 'nights', 'adults')) !!}
                </div>
            @endforeach
        </div>

        @if ($rooms instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
            <div class="text-center mt-30">
                {!! $rooms->withQueryString()->links(Theme::getThemeNamespace('partials.pagination')) !!}
            </div>
        @endif
    @else
        <p>{{ __('Derzeit sind keine Räume verfügbar') }}</p>
    @endif
</section>

<section class="services-area pt-90 pb-90">
    <div class="container">
        <div class="row g-4 justify-content-center">
            @foreach ($rooms as $room)
                <div class="col-12 col-md-6 col-lg-5">
                    {!! Theme::partial('rooms.item', compact('room', 'startDate', 'endDate', 'nights', 'adults')) !!}
                </div>
            @endforeach
        </div>
    </div>
</section>

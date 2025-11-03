@extends(HotelHelper::viewPath('customers.master'))

@section('content')
    <div class="customer-card">
        <div class="customer-card-header">
            <h2 class="customer-card-title">{{ SeoHelper::getTitle() }}</h2>
            <p class="customer-card-subtitle">{{ __('See all of your Inspira course reservations in one streamlined view.') }}</p>
        </div>

        <div class="customer-card-body">
            @if ($courseBookings->count() > 0)
                <div class="booking-card-list">
                    @foreach ($courseBookings as $booking)
                        @php
                            $course = $booking->course;
                            $session = $booking->session;
                            $thumbnail = $course?->image;
                            $title = $course?->name ?? __('Course removed');
                            $courseUrl = $course?->url;
                            $subtitleParts = array_filter([
                                $course?->instructor?->name,
                                $course?->category?->name,
                            ]);
                            $subtitle = implode(' • ', $subtitleParts);
                            $start = $session?->start_date ? \Carbon\Carbon::parse($session->start_date)->format('d M Y H:i') : null;
                            $end = $session?->end_date ? \Carbon\Carbon::parse($session->end_date)->format('d M Y H:i') : null;
                            $statusClass = 'booking-status--' . \Illuminate\Support\Str::slug($booking->status->getValue());
                            $invoiceId = optional($booking->invoice)->getKey();
                        @endphp

                        <article class="booking-card">
                            <div class="booking-card-media">
                                <img src="{{ RvMedia::getImageUrl($thumbnail, 'thumb', false, RvMedia::getDefaultImage()) }}" alt="{{ $title }}">
                            </div>

                            <div class="booking-card-details">
                                <div class="booking-card-heading">
                                    <h3 class="booking-card-title">
                                        @if ($courseUrl)
                                            <a href="{{ $courseUrl }}" target="_blank" rel="noopener">{{ $title }}</a>
                                        @else
                                            {{ $title }}
                                        @endif
                                    </h3>

                                    <span class="booking-status {{ $statusClass }}">{{ $booking->status->label() }}</span>
                                </div>

                                @if ($subtitle)
                                    <p class="booking-card-subtitle">{{ $subtitle }}</p>
                                @endif

                                <div class="booking-card-meta">
                                    <span><i class="fal fa-calendar"></i>{{ $start && $end ? $start . ' – ' . $end : __('Schedule pending') }}</span>
                                    <span><i class="fal fa-clock"></i>{{ __('Booked on :date', ['date' => $booking->created_at->format('d M Y')]) }}</span>
                                    <span><i class="fal fa-hashtag"></i>{{ $booking->booking_number }}</span>
                                </div>
                            </div>

                            <div class="booking-card-summary">
                                <div class="booking-card-price">{{ format_price($booking->amount) }}</div>

                                <div class="booking-card-actions">
                                    <a class="booking-card-action" href="{{ $courseUrl ?? route('customer.course-bookings') }}" title="{{ __('Course details') }}">
                                        <i class="fal fa-calendar-day" aria-hidden="true"></i>
                                        <span class="visually-hidden">{{ __('Course details') }}</span>
                                    </a>

                                    @if ($invoiceId)
                                        <a class="booking-card-action" href="{{ route('customer.generate-invoice', $invoiceId) }}" title="{{ __('Download invoice') }}" data-bs-toggle="tooltip">
                                            <i class="fal fa-download" aria-hidden="true"></i>
                                            <span class="visually-hidden">{{ __('Download invoice') }}</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="customer-empty-state">
                    <i class="fal fa-calendar-times" aria-hidden="true"></i>
                    <p>{{ __('No course bookings!') }}</p>
                </div>
            @endif
        </div>

        <div class="customer-card-footer">
            {!! $courseBookings->links(Theme::getThemeNamespace('partials.pagination')) !!}
        </div>
    </div>
@stop

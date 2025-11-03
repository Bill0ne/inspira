@extends(HotelHelper::viewPath('customers.master'))

@section('content')
    <div class="customer-card">
        <div class="customer-card-header">
            <h2 class="customer-card-title">{{ SeoHelper::getTitle() }}</h2>
            <p class="customer-card-subtitle">{{ __('Behalten Sie jede Buchung im Blick – inklusive Status, Zeitplan und Rechnungszugriff.') }}</p>
        </div>

        <div class="customer-card-body">
            @if ($bookings->count() > 0)
                <div class="booking-card-list">
                    @foreach ($bookings as $item)
                        @php
                            $type = $item['type'];
                            $booking = $item['model'];
                            $statusClass = 'booking-status--' . \Illuminate\Support\Str::slug($booking->status->getValue());
                            $invoice = $booking->invoice;
                            $invoiceId = $invoice instanceof \Illuminate\Database\Eloquent\Model
                                ? $invoice->getKey()
                                : data_get($invoice, 'id');
                        @endphp

                        @if ($type === 'course')
                            @php
                                $course = $booking->course;
                                $session = $booking->session;
                                $thumbnail = $course?->thumbnail ?? $course?->image;
                                $title = $course?->name ?? __('Kurs entfernt');
                                $courseUrl = $course?->url;
                                $subtitleParts = array_filter([
                                    $course?->instructor?->name,
                                    $course?->category?->name,
                                ]);
                                $subtitle = implode(' • ', $subtitleParts);
                                $start = $session?->start_date ? \Carbon\Carbon::parse($session->start_date)->format('d.m.Y H:i') : null;
                                $end = $session?->end_date ? \Carbon\Carbon::parse($session->end_date)->format('d.m.Y H:i') : null;
                                $detailUrl = $courseUrl ?? route('customer.bookings');
                            @endphp

                            <article class="booking-card booking-card--course">
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
                                        <span>
                                            <i class="fal fa-calendar"></i>
                                            {{ $start && $end ? $start . ' – ' . $end : __('Termin steht noch aus') }}
                                        </span>
                                        <span><i class="fal fa-clock"></i>{{ __('Gebucht am :date', ['date' => $booking->created_at->format('d.m.Y')]) }}</span>
                                        <span><i class="fal fa-hashtag"></i>{{ $booking->booking_number }}</span>
                                    </div>
                                </div>

                                <div class="booking-card-summary">
                                    <div class="booking-card-price">{{ format_price($booking->amount) }}</div>

                                    <div class="booking-card-actions">
                                        <a class="booking-card-action" href="{{ $detailUrl }}" title="{{ __('Kursdetails') }}">
                                            <i class="fal fa-calendar-day" aria-hidden="true"></i>
                                            <span class="visually-hidden">{{ __('Kursdetails') }}</span>
                                        </a>

                                        @if ($invoiceId)
                                            <a class="booking-card-action" href="{{ route('customer.generate-invoice', $invoiceId) }}" title="{{ __('Rechnung herunterladen') }}" data-bs-toggle="tooltip">
                                                <i class="fal fa-download" aria-hidden="true"></i>
                                                <span class="visually-hidden">{{ __('Rechnung herunterladen') }}</span>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @else
                            @php
                                $roomRelation = $booking->room->room;
                                $hasRoom = $roomRelation && $roomRelation->exists;
                                $roomName = $hasRoom ? $roomRelation->name : ($booking->room->name ?? $booking->room->room_name);
                                $roomUrl = $hasRoom ? $roomRelation->url : null;
                                $roomImage = $hasRoom ? $roomRelation->image : $booking->room->room_image;
                                $category = $hasRoom ? optional($roomRelation->category)->name : null;
                                $start = $booking->room->start_date ? \Carbon\Carbon::parse($booking->room->start_date)->format('d.m.Y H:i') : null;
                                $end = $booking->room->end_date ? \Carbon\Carbon::parse($booking->room->end_date)->format('d.m.Y H:i') : null;
                            @endphp

                            <article class="booking-card booking-card--room">
                                <div class="booking-card-media">
                                    <img src="{{ RvMedia::getImageUrl($roomImage, 'thumb', false, RvMedia::getDefaultImage()) }}" alt="{{ $roomName }}">
                                </div>

                                <div class="booking-card-details">
                                    <div class="booking-card-heading">
                                        <h3 class="booking-card-title">
                                            @if ($roomUrl)
                                                <a href="{{ $roomUrl }}" target="_blank" rel="noopener">{{ $roomName }}</a>
                                            @else
                                                {{ $roomName }}
                                            @endif
                                        </h3>

                                        <span class="booking-status {{ $statusClass }}">{{ $booking->status->label() }}</span>
                                    </div>

                                    @if ($category)
                                        <p class="booking-card-subtitle">{{ $category }}</p>
                                    @endif

                                    <div class="booking-card-meta">
                                        @if ($start && $end)
                                            <span><i class="fal fa-calendar"></i>{{ $start }} – {{ $end }}</span>
                                        @elseif ($booking->room->booking_period)
                                            <span><i class="fal fa-calendar"></i>{{ $booking->room->booking_period }}</span>
                                        @endif
                                        <span><i class="fal fa-clock"></i>{{ __('Gebucht am :date', ['date' => $booking->created_at->format('d.m.Y')]) }}</span>
                                        <span><i class="fal fa-hashtag"></i>{{ $booking->booking_number }}</span>
                                    </div>
                                </div>

                                <div class="booking-card-summary">
                                    <div class="booking-card-price">{{ format_price($booking->amount) }}</div>

                                    <div class="booking-card-actions">
                                        <a class="booking-card-action" href="{{ route('customer.bookings.show', $booking->transaction_id) }}" title="{{ __('Details anzeigen') }}">
                                            <i class="fal fa-calendar-day" aria-hidden="true"></i>
                                            <span class="visually-hidden">{{ __('Details anzeigen') }}</span>
                                        </a>

                                        @if ($invoiceId)
                                            <a class="booking-card-action" href="{{ route('customer.generate-invoice', $invoiceId) }}" title="{{ __('Rechnung herunterladen') }}" data-bs-toggle="tooltip">
                                                <i class="fal fa-download" aria-hidden="true"></i>
                                                <span class="visually-hidden">{{ __('Rechnung herunterladen') }}</span>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @endif
                    @endforeach
                </div>
            @else
                <div class="customer-empty-state">
                    <i class="fal fa-calendar-times" aria-hidden="true"></i>
                    <p>{{ __('Keine Buchungen!') }}</p>
                </div>
            @endif
        </div>

        <div class="customer-card-footer">
            {!! $bookings->links(Theme::getThemeNamespace('partials.pagination')) !!}
        </div>
    </div>
@stop

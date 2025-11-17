@extends(HotelHelper::viewPath('customers.master'))

@push('styles')
    <style>
        .inspira-modal .modal-content {
            border-radius: 18px;
            border: 1px solid rgba(87, 142, 136, 0.18);
            background: linear-gradient(135deg, #ffffff 0%, #f6fbfa 100%);
            box-shadow: 0 18px 36px rgba(24, 50, 46, 0.12);
        }

        .inspira-modal .modal-header {
            border-bottom: none;
            padding-bottom: 0;
        }

        .inspira-modal .modal-body {
            padding-top: 0;
        }

        .inspira-policy-alert {
            background: rgba(87, 142, 136, 0.1);
            border-radius: 14px;
            padding: 18px;
            margin-bottom: 18px;
            display: flex;
            gap: 12px;
            align-items: flex-start;
            border: 1px solid rgba(87, 142, 136, 0.18);
        }

        .inspira-policy-alert .icon {
            color: #578E88;
            font-size: 20px;
            line-height: 1;
        }

        .inspira-policy-alert .content {
            color: #1b2e2a;
            font-size: 14px;
            line-height: 1.6;
        }

        .inspira-refund-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 12px;
            margin-bottom: 16px;
        }

        .inspira-refund-card {
            background: #f9fbfb;
            border-radius: 14px;
            padding: 16px;
            border: 1px solid rgba(87, 142, 136, 0.14);
            box-shadow: 0 10px 24px rgba(87, 142, 136, 0.09);
        }

        .inspira-refund-card .label {
            font-size: 12px;
            text-transform: uppercase;
            color: #7f908c;
            letter-spacing: 0.05em;
            margin-bottom: 6px;
        }

        .inspira-refund-card .value {
            font-size: 16px;
            font-weight: 600;
            color: #2d4d48;
        }

        .inspira-modal .form-check {
            background: rgba(255, 181, 71, 0.08);
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 181, 71, 0.2);
        }

        .inspira-modal .modal-footer {
            border-top: none;
            gap: 10px;
        }

        .inspira-form-feedback {
            font-size: 14px;
            margin-bottom: 12px;
        }

        .inspira-form-feedback.text-success {
            color: #1b7d43;
        }

        .inspira-form-feedback.text-danger {
            color: #c0392b;
        }
    </style>
@endpush

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
                                  $cancellationQuote = class_exists(\Botble\InspiraCancellation\Facades\InspiraCancellation::class)
                                      ? \Botble\InspiraCancellation\Facades\InspiraCancellation::getCancellationQuote('course', $booking)
                                      : null;
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
                                        @if (class_exists(\Botble\InspiraCancellation\Facades\InspiraCancellation::class))
                                            <button
                                                type="button"
                                                class="booking-card-action"
                                                data-bs-toggle="modal"
                                                data-bs-target="#inspiraCancel-course-{{ $booking->getKey() }}"
                                                title="{{ trans('plugins/inspira-cancellation::cancellation.frontend.cancel') }}"
                                            >
                                                <i class="fal fa-times" aria-hidden="true"></i>
                                                <span class="visually-hidden">{{ trans('plugins/inspira-cancellation::cancellation.frontend.cancel') }}</span>
                                            </button>

                                            <button
                                                type="button"
                                                class="booking-card-action"
                                                data-bs-toggle="modal"
                                                data-bs-target="#inspiraTransfer-course-{{ $booking->getKey() }}"
                                                title="{{ trans('plugins/inspira-cancellation::cancellation.frontend.replace') }}"
                                            >
                                                <i class="fal fa-exchange-alt" aria-hidden="true"></i>
                                                <span class="visually-hidden">{{ trans('plugins/inspira-cancellation::cancellation.frontend.replace') }}</span>
                                            </button>
                                        @endif

                                        <a class="booking-card-action" href="{{ $detailUrl }}" title="{{ __('Kursdetails') }}">
                                            <i class="fal fa-eye" aria-hidden="true"></i>
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

                            @if (class_exists(\Botble\InspiraCancellation\Facades\InspiraCancellation::class))
                                @php
                                    $cancelModalId = 'inspiraCancel-course-' . $booking->getKey();
                                    $transferModalId = 'inspiraTransfer-course-' . $booking->getKey();
                                    $activeRule = $cancellationQuote['rule'] ?? null;
                                    $ruleDescription = $activeRule->description ?? null;
                                    $refundAmount = $cancellationQuote['refund_amount'] ?? 0;
                                    $refundPercent = $cancellationQuote['refund_percent'] ?? 0;
                                    $feeAmount = $cancellationQuote['fee_amount'] ?? 0;
                                    $daysUntilStart = $cancellationQuote['days_until_start'] ?? null;
                                    $feePercent = max(0, min(100, 100 - (int) $refundPercent));
                                @endphp

                                <div class="modal fade inspira-modal" id="{{ $cancelModalId }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{ trans('plugins/inspira-cancellation::cancellation.frontend.cancel') }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ trans('core/base::forms.cancel') }}"></button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="inspira-policy-alert">
                                                    <span class="icon"><i class="fal fa-info-circle" aria-hidden="true"></i></span>
                                                    <div class="content">
                                                        {{ trans('plugins/inspira-cancellation::cancellation.frontend.guideline') }}<br>
                                                        {{ $ruleDescription ?: trans('plugins/inspira-cancellation::cancellation.frontend.no_active_rule') }}
                                                    </div>
                                                </div>

                                                <ul class="list-unstyled text-muted small mb-3">
                                                    <li><strong>{{ $title }}</strong></li>
                                                    <li>{{ __('Gebucht am :date', ['date' => $booking->created_at->format('d.m.Y')]) }}</li>
                                                    <li>{{ $start && $end ? $start . ' – ' . $end : __('Termin steht noch aus') }}</li>
                                                </ul>

                                                <div class="inspira-refund-summary">
                                                    <div class="inspira-refund-card">
                                                        <div class="label">{{ trans('plugins/inspira-cancellation::cancellation.frontend.refund_amount') }}</div>
                                                        <div class="value">{{ format_price($refundAmount) }}</div>
                                                    </div>
                                                    <div class="inspira-refund-card">
                                                        <div class="label">{{ trans('plugins/inspira-cancellation::cancellation.rule.refund_percent') }}</div>
                                                        <div class="value">{{ $refundPercent }}%</div>
                                                    </div>
                                                    <div class="inspira-refund-card">
                                                        <div class="label">{{ trans('plugins/inspira-cancellation::cancellation.frontend.fee_amount') }}</div>
                                                        <div class="value">{{ format_price($feeAmount) }}</div>
                                                    </div>
                                                    <div class="inspira-refund-card">
                                                        <div class="label">{{ trans('plugins/inspira-cancellation::cancellation.frontend.fee_percent') }}</div>
                                                        <div class="value">{{ $feePercent }}%</div>
                                                    </div>
                                                </div>

                                                @if (! is_null($daysUntilStart))
                                                    <p class="text-muted small mb-3">
                                                        <i class="fal fa-calendar-day me-2" aria-hidden="true"></i>
                                                        {{ trans('plugins/inspira-cancellation::cancellation.frontend.days_until_start', ['days' => $daysUntilStart]) }}
                                                    </p>
                                                @endif

                                                @if ($activeRule)
                                                    <p class="text-muted small mb-3">
                                                        <i class="fal fa-clipboard-list me-2" aria-hidden="true"></i>
                                                        @if (! is_null($activeRule->from_days) && ! is_null($activeRule->to_days))
                                                            {{ trans('plugins/inspira-cancellation::cancellation.frontend.rule_between', ['from' => $activeRule->from_days, 'to' => $activeRule->to_days]) }}
                                                        @elseif (! is_null($activeRule->from_days))
                                                            {{ trans('plugins/inspira-cancellation::cancellation.frontend.rule_from', ['from' => $activeRule->from_days]) }}
                                                        @elseif (! is_null($activeRule->to_days))
                                                            {{ trans('plugins/inspira-cancellation::cancellation.frontend.rule_to', ['to' => $activeRule->to_days]) }}
                                                        @else
                                                            {{ trans('plugins/inspira-cancellation::cancellation.frontend.rule_open') }}
                                                        @endif
                                                    </p>
                                                @endif

                                                <p class="text-muted small mb-3">
                                                    <i class="fal fa-shield-check me-2" aria-hidden="true"></i>
                                                    {{ trans('plugins/inspira-cancellation::cancellation.messages.cancellation_pending_manual_review') }}
                                                </p>

                                                <form
                                                    class="inspira-action-form js-cancellation-form"
                                                    action="{{ route('customer.bookings.cancel', ['course', $booking->getKey()]) }}"
                                                    method="POST"
                                                    data-modal-target="#{{ $cancelModalId }}"
                                                >
                                                    @csrf
                                                    <div class="mb-3">
                                                        <label class="form-label" for="cancel-note-{{ $booking->getKey() }}">
                                                            {{ trans('plugins/inspira-cancellation::cancellation.frontend.notes_label') }}
                                                        </label>
                                                        <textarea
                                                            class="form-control"
                                                            id="cancel-note-{{ $booking->getKey() }}"
                                                            name="notes"
                                                            rows="3"
                                                            placeholder="{{ trans('plugins/inspira-cancellation::cancellation.frontend.notes_placeholder') }}"
                                                        ></textarea>
                                                    </div>

                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" value="1" id="cancel-confirm-{{ $booking->getKey() }}" name="accept_terms" required>
                                                        <label class="form-check-label" for="cancel-confirm-{{ $booking->getKey() }}">
                                                            {{ trans('plugins/inspira-cancellation::cancellation.frontend.accept_terms') }}
                                                        </label>
                                                    </div>

                                                    <div class="inspira-form-feedback d-none"></div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ trans('core/base::forms.cancel') }}</button>
                                                        <button type="submit" class="btn btn-danger">{{ trans('plugins/inspira-cancellation::cancellation.frontend.confirm_cancellation') }}</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal fade inspira-modal" id="{{ $transferModalId }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{ trans('plugins/inspira-cancellation::cancellation.frontend.replace') }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ trans('core/base::forms.cancel') }}"></button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="inspira-policy-alert">
                                                    <span class="icon"><i class="fal fa-info-circle" aria-hidden="true"></i></span>
                                                    <div class="content">{{ trans('plugins/inspira-cancellation::cancellation.frontend.replacement_intro') }}</div>
                                                </div>

                                                <p class="text-muted small mb-3">
                                                    <i class="fal fa-user-shield me-2" aria-hidden="true"></i>
                                                    {{ trans('plugins/inspira-cancellation::cancellation.messages.replacement_pending') }}
                                                </p>

                                                <form
                                                    class="inspira-action-form js-transfer-form"
                                                    action="{{ route('customer.bookings.transfer', ['course', $booking->getKey()]) }}"
                                                    method="POST"
                                                    data-modal-target="#{{ $transferModalId }}"
                                                >
                                                    @csrf
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label" for="transfer-first-name-{{ $booking->getKey() }}">{{ trans('plugins/hotel::booking.first_name') }}</label>
                                                            <input type="text" class="form-control" id="transfer-first-name-{{ $booking->getKey() }}" name="first_name" required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label" for="transfer-last-name-{{ $booking->getKey() }}">{{ trans('plugins/hotel::booking.last_name') }}</label>
                                                            <input type="text" class="form-control" id="transfer-last-name-{{ $booking->getKey() }}" name="last_name" required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label" for="transfer-email-{{ $booking->getKey() }}">{{ trans('plugins/hotel::booking.email') }}</label>
                                                            <input type="email" class="form-control" id="transfer-email-{{ $booking->getKey() }}" name="email" required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label" for="transfer-phone-{{ $booking->getKey() }}">{{ trans('plugins/hotel::booking.phone') }}</label>
                                                            <input type="text" class="form-control" id="transfer-phone-{{ $booking->getKey() }}" name="phone">
                                                        </div>
                                                    </div>

                                                    <div class="inspira-form-feedback d-none"></div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ trans('core/base::forms.cancel') }}</button>
                                                        <button type="submit" class="btn btn-success">{{ trans('plugins/inspira-cancellation::cancellation.frontend.confirm_transfer') }}</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
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
                                  $cancellationQuote = class_exists(\Botble\InspiraCancellation\Facades\InspiraCancellation::class)
                                      ? \Botble\InspiraCancellation\Facades\InspiraCancellation::getCancellationQuote('room', $booking)
                                      : null;
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
                                            @if (class_exists(\Botble\InspiraCancellation\Facades\InspiraCancellation::class))
                                                <button
                                                    type="button"
                                                    class="booking-card-action"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#inspiraCancel-room-{{ $booking->getKey() }}"
                                                    title="{{ trans('plugins/inspira-cancellation::cancellation.frontend.cancel') }}"
                                                >
                                                    <i class="fal fa-times" aria-hidden="true"></i>
                                                    <span class="visually-hidden">{{ trans('plugins/inspira-cancellation::cancellation.frontend.cancel') }}</span>
                                                </button>
                                            @endif

                                            <a class="booking-card-action" href="{{ route('customer.bookings.show', $booking->transaction_id) }}" title="{{ __('Details anzeigen') }}">
                                                <i class="fal fa-eye" aria-hidden="true"></i>
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

                            @if (class_exists(\Botble\InspiraCancellation\Facades\InspiraCancellation::class))
                                @php
                                    $cancelModalId = 'inspiraCancel-room-' . $booking->getKey();
                                    $activeRule = $cancellationQuote['rule'] ?? null;
                                    $ruleDescription = $activeRule->description ?? null;
                                    $refundAmount = $cancellationQuote['refund_amount'] ?? 0;
                                    $refundPercent = $cancellationQuote['refund_percent'] ?? 0;
                                    $feeAmount = $cancellationQuote['fee_amount'] ?? 0;
                                    $daysUntilStart = $cancellationQuote['days_until_start'] ?? null;
                                    $feePercent = max(0, min(100, 100 - (int) $refundPercent));
                                @endphp

                                <div class="modal fade inspira-modal" id="{{ $cancelModalId }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{ trans('plugins/inspira-cancellation::cancellation.frontend.cancel') }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ trans('core/base::forms.cancel') }}"></button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="inspira-policy-alert">
                                                    <span class="icon"><i class="fal fa-info-circle" aria-hidden="true"></i></span>
                                                    <div class="content">
                                                        {{ trans('plugins/inspira-cancellation::cancellation.frontend.guideline') }}<br>
                                                        {{ $ruleDescription ?: trans('plugins/inspira-cancellation::cancellation.frontend.no_active_rule') }}
                                                    </div>
                                                </div>

                                                <ul class="list-unstyled text-muted small mb-3">
                                                    <li><strong>{{ $roomName }}</strong></li>
                                                    <li>{{ __('Gebucht am :date', ['date' => $booking->created_at->format('d.m.Y')]) }}</li>
                                                    @if ($start && $end)
                                                        <li>{{ $start }} – {{ $end }}</li>
                                                    @elseif ($booking->room->booking_period)
                                                        <li>{{ $booking->room->booking_period }}</li>
                                                    @endif
                                                </ul>

                                                <div class="inspira-refund-summary">
                                                    <div class="inspira-refund-card">
                                                        <div class="label">{{ trans('plugins/inspira-cancellation::cancellation.frontend.refund_amount') }}</div>
                                                        <div class="value">{{ format_price($refundAmount) }}</div>
                                                    </div>
                                                    <div class="inspira-refund-card">
                                                        <div class="label">{{ trans('plugins/inspira-cancellation::cancellation.rule.refund_percent') }}</div>
                                                        <div class="value">{{ $refundPercent }}%</div>
                                                    </div>
                                                    <div class="inspira-refund-card">
                                                        <div class="label">{{ trans('plugins/inspira-cancellation::cancellation.frontend.fee_amount') }}</div>
                                                        <div class="value">{{ format_price($feeAmount) }}</div>
                                                    </div>
                                                    <div class="inspira-refund-card">
                                                        <div class="label">{{ trans('plugins/inspira-cancellation::cancellation.frontend.fee_percent') }}</div>
                                                        <div class="value">{{ $feePercent }}%</div>
                                                    </div>
                                                </div>

                                                @if (! is_null($daysUntilStart))
                                                    <p class="text-muted small mb-3">
                                                        <i class="fal fa-calendar-day me-2" aria-hidden="true"></i>
                                                        {{ trans('plugins/inspira-cancellation::cancellation.frontend.days_until_start', ['days' => $daysUntilStart]) }}
                                                    </p>
                                                @endif

                                                @if ($activeRule)
                                                    <p class="text-muted small mb-3">
                                                        <i class="fal fa-clipboard-list me-2" aria-hidden="true"></i>
                                                        @if (! is_null($activeRule->from_days) && ! is_null($activeRule->to_days))
                                                            {{ trans('plugins/inspira-cancellation::cancellation.frontend.rule_between', ['from' => $activeRule->from_days, 'to' => $activeRule->to_days]) }}
                                                        @elseif (! is_null($activeRule->from_days))
                                                            {{ trans('plugins/inspira-cancellation::cancellation.frontend.rule_from', ['from' => $activeRule->from_days]) }}
                                                        @elseif (! is_null($activeRule->to_days))
                                                            {{ trans('plugins/inspira-cancellation::cancellation.frontend.rule_to', ['to' => $activeRule->to_days]) }}
                                                        @else
                                                            {{ trans('plugins/inspira-cancellation::cancellation.frontend.rule_open') }}
                                                        @endif
                                                    </p>
                                                @endif

                                                <p class="text-muted small mb-3">
                                                    <i class="fal fa-shield-check me-2" aria-hidden="true"></i>
                                                    {{ trans('plugins/inspira-cancellation::cancellation.messages.cancellation_pending_manual_review') }}
                                                </p>

                                                <form
                                                    class="inspira-action-form js-cancellation-form"
                                                    action="{{ route('customer.bookings.cancel', ['room', $booking->getKey()]) }}"
                                                    method="POST"
                                                    data-modal-target="#{{ $cancelModalId }}"
                                                >
                                                    @csrf
                                                    <div class="mb-3">
                                                        <label class="form-label" for="cancel-note-room-{{ $booking->getKey() }}">
                                                            {{ trans('plugins/inspira-cancellation::cancellation.frontend.notes_label') }}
                                                        </label>
                                                        <textarea
                                                            class="form-control"
                                                            id="cancel-note-room-{{ $booking->getKey() }}"
                                                            name="notes"
                                                            rows="3"
                                                            placeholder="{{ trans('plugins/inspira-cancellation::cancellation.frontend.notes_placeholder') }}"
                                                        ></textarea>
                                                    </div>

                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" value="1" id="cancel-confirm-room-{{ $booking->getKey() }}" name="accept_terms" required>
                                                        <label class="form-check-label" for="cancel-confirm-room-{{ $booking->getKey() }}">
                                                            {{ trans('plugins/inspira-cancellation::cancellation.frontend.accept_terms') }}
                                                        </label>
                                                    </div>

                                                    <div class="inspira-form-feedback d-none"></div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ trans('core/base::forms.cancel') }}</button>
                                                        <button type="submit" class="btn btn-danger">{{ trans('plugins/inspira-cancellation::cancellation.frontend.confirm_cancellation') }}</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

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

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const defaultError = @json(trans('plugins/inspira-cancellation::cancellation.frontend.error_message'));

            const handleSubmit = (form) => {
                const successMessage = form.classList.contains('js-transfer-form')
                    ? @json(trans('plugins/inspira-cancellation::cancellation.messages.replacement_pending'))
                    : @json(trans('plugins/inspira-cancellation::cancellation.messages.cancellation_success'));

                form.addEventListener('submit', async (event) => {
                    event.preventDefault();

                    const submitButton = form.querySelector('button[type="submit"]');
                    const feedback = form.querySelector('.inspira-form-feedback');

                    if (feedback) {
                        feedback.classList.add('d-none');
                        feedback.classList.remove('text-danger', 'text-success');
                        feedback.textContent = '';
                    }

                    submitButton?.setAttribute('disabled', 'disabled');

                    try {
                        const response = await fetch(form.action, {
                            method: 'POST',
                            body: new FormData(form),
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        });

                        const data = await response.json().catch(() => ({}));

                        if (!response.ok || data.error) {
                            throw new Error(data.message || defaultError);
                        }

                        if (feedback) {
                            feedback.textContent = data.message || successMessage;
                            feedback.classList.remove('d-none');
                            feedback.classList.add('text-success');
                        }

                        setTimeout(() => window.location.reload(), 1200);
                    } catch (error) {
                        if (feedback) {
                            feedback.textContent = error.message || defaultError;
                            feedback.classList.remove('d-none');
                            feedback.classList.add('text-danger');
                        }
                    } finally {
                        submitButton?.removeAttribute('disabled');
                    }
                });
            };

            document.querySelectorAll('.js-cancellation-form, .js-transfer-form').forEach((form) => handleSubmit(form));
        });
    </script>
@endpush

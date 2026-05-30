@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    {!! do_action('booking_reports_before_component_render') !!}

    @if (! ($manualBookingsEnabled ?? true))
        <div class="alert alert-warning d-flex align-items-center" role="alert">
            <i class="ti ti-alert-triangle me-2"></i>
            <div>{{ trans('plugins/hotel::booking.manual_booking_table_missing') }}</div>
        </div>
    @endif

    <calendar-booking-reports-component
        v-cloak
        events-url="{{ route('booking.reports.records.index') }}"
        kpis-url="{{ route('booking.calendar.kpis') }}"
    >
        <template v-slot:title>
            {{ trans('plugins/hotel::booking.calendar') }}
        </template>
        <template v-slot:actions>
            <x-core::button
                color="primary"
                icon="ti ti-plus"
                data-bs-toggle="modal"
                data-bs-target="#manual-booking-modal"
            >
                {{ trans('plugins/hotel::booking.manual_booking') }}
            </x-core::button>
        </template>

        <template v-slot:loading>
            @include('core/base::elements.loading')
        </template>
    </calendar-booking-reports-component>

    <x-core::modal
        id="manual-booking-modal"
        type="info"
        :title="trans('plugins/hotel::booking.manual_booking')"
        size="lg"
    >
        <form method="POST" action="{{ route('booking.calendar.manual.store') }}" id="manual-booking-form">
            @csrf
            <div class="row g-3">
                <div class="col-lg-4">
                    <label class="form-label" for="manual-booking-type">{{ trans('plugins/hotel::booking.manual_booking_type') }}</label>
                    <select class="form-select" id="manual-booking-type" name="type">
                        <option value="room">{{ trans('plugins/hotel::booking.manual_booking_type_room') }}</option>
                        <option value="course">{{ trans('plugins/hotel::booking.manual_booking_type_course') }}</option>
                    </select>
                </div>
                <div class="col-lg-4" data-manual-booking-target="room">
                    <label class="form-label" for="manual-booking-room">{{ trans('plugins/hotel::booking.manual_booking_select_rooms') }}</label>
                    <select class="form-select select-search-full" id="manual-booking-room" name="room_id[]" multiple data-placeholder="{{ trans('plugins/hotel::booking.manual_booking_select_rooms') }}">
                        @foreach ($rooms as $room)
                            <option value="{{ $room->id }}">{{ $room->name }}</option>
                        @endforeach
                    </select>
                    <small class="form-hint">{{ trans('plugins/hotel::booking.manual_booking_select_rooms_hint') }}</small>
                </div>
                <div class="col-lg-4 d-none" data-manual-booking-target="course">
                    <label class="form-label" for="manual-booking-course">{{ trans('plugins/hotel::booking.manual_booking_type_course') }}</label>
                    <select class="form-select" id="manual-booking-course" name="course_id" disabled>
                        <option value="">{{ trans('plugins/hotel::booking.manual_booking_select_course') }}</option>
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-6">
                    <x-core::form.date-picker
                        name="start_at"
                        id="manual-booking-start"
                        :label="trans('plugins/hotel::booking.start_date')"
                        data-date-format="Y-m-d H:i"
                        :data-options="[
                            'enableTime' => true,
                            'time_24hr' => true,
                            'minuteIncrement' => 15,
                            'dateFormat' => 'Y-m-d H:i',
                        ]"
                    />
                </div>
                <div class="col-lg-6">
                    <x-core::form.date-picker
                        name="end_at"
                        id="manual-booking-end"
                        :label="trans('plugins/hotel::booking.end_date')"
                        data-date-format="Y-m-d H:i"
                        :data-options="[
                            'enableTime' => true,
                            'time_24hr' => true,
                            'minuteIncrement' => 15,
                            'dateFormat' => 'Y-m-d H:i',
                        ]"
                    />
                </div>
                <div class="col-12">
                    <label class="form-label" for="manual-booking-reason">{{ trans('plugins/hotel::booking.manual_booking_reason') }}</label>
                    <textarea class="form-control" id="manual-booking-reason" name="reason" rows="3"></textarea>
                </div>
            </div>
        </form>

        <x-slot name="footer">
            <x-core::button data-bs-dismiss="modal">
                {{ trans('core/base::forms.cancel') }}
            </x-core::button>
            <x-core::button color="primary" type="submit" form="manual-booking-form">
                {{ trans('core/base::forms.save') }}
            </x-core::button>
        </x-slot>
    </x-core::modal>

    {!! do_action('booking_reports_after_component_render') !!}
@endsection

@push('footer')
    <script>
        (function () {
            const applyToggle = () => {
                const typeSelect = document.getElementById('manual-booking-type')
                const roomTarget = document.querySelector('[data-manual-booking-target="room"]')
                const courseTarget = document.querySelector('[data-manual-booking-target="course"]')
                const courseSelect = document.getElementById('manual-booking-course')

                if (! typeSelect || ! roomTarget || ! courseTarget) return

                const isRoom = typeSelect.value === 'room'
                roomTarget.classList.toggle('d-none', !isRoom)
                courseTarget.classList.toggle('d-none', isRoom)

                // Das inaktive Ziel wird zusätzlich serverseitig geleert
                // (ManualBookingRequest::prepareForValidation), daher reicht hier
                // das Ein-/Ausblenden. Das Course-Feld (natives Select) wird
                // deaktiviert; das Raum-Select nutzt select2 und wird nicht per
                // .disabled angefasst, um Sync-Probleme zu vermeiden.
                if (courseSelect) courseSelect.disabled = isRoom
            }

            // Direkter Listener (falls Element bereits im DOM)
            document.getElementById('manual-booking-type')?.addEventListener('change', applyToggle)

            // Delegation (falls Modal nachträglich gerendert wird)
            document.addEventListener('change', (event) => {
                if (event.target && event.target.id === 'manual-booking-type') {
                    applyToggle()
                }
            })

            // Beim Öffnen des Modals erneut anwenden
            const modal = document.getElementById('manual-booking-modal')
            modal?.addEventListener('shown.bs.modal', applyToggle)

            // Initial-State setzen
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', applyToggle)
            } else {
                applyToggle()
            }
        })()
    </script>
    <style>
        /* Smart Calendar - Toolbar */
        .fc .fc-toolbar-title {
            font-size: 1.25rem !important;
            font-weight: 600;
        }

        .fc .fc-button {
            font-size: 0.8125rem;
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            text-transform: none;
        }

        .fc .fc-button-primary {
            background-color: var(--bb-primary, #206bc4);
            border-color: var(--bb-primary, #206bc4);
        }

        .fc .fc-button-primary:not(:disabled).fc-button-active,
        .fc .fc-button-primary:not(:disabled):active {
            background-color: var(--bb-primary, #206bc4);
            border-color: var(--bb-primary, #206bc4);
            opacity: 0.9;
        }

        .fc .fc-today-button {
            font-weight: 600;
        }

        /* Today column highlight */
        .fc .fc-day-today {
            background-color: rgba(var(--bb-primary-rgb, 32, 107, 196), 0.04) !important;
        }

        /* Time grid - slot styling */
        .fc .fc-timegrid-slot {
            height: 2.5em;
        }

        .fc .fc-timegrid-slot-label {
            font-size: 0.75rem;
            color: #6c757d;
            font-weight: 500;
        }

        .fc .fc-timegrid-axis {
            font-size: 0.75rem;
            color: #6c757d;
        }

        .fc .fc-timegrid-now-indicator-line {
            border-color: #e53e3e;
            border-width: 2px;
        }

        .fc .fc-timegrid-now-indicator-arrow {
            border-top-color: #e53e3e;
        }

        /* Chip-style events - TimeGrid */
        .fc .fc-timegrid-event {
            border-radius: 16px !important;
            font-size: 11.5px !important;
            font-weight: 500;
            padding: 2px 8px !important;
            border: none !important;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin: 1px 2px !important;
            overflow: hidden;
        }

        .fc .fc-timegrid-event .fc-event-main {
            padding: 2px 4px;
            overflow: hidden;
        }

        .fc .fc-timegrid-event .fc-event-title {
            font-size: 11px;
            line-height: 1.3;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .fc .fc-timegrid-event .fc-event-time {
            font-size: 10px;
            font-weight: 600;
            opacity: 0.8;
        }

        /* Chip-style events - DayGrid (Monatsansicht) */
        .fc .fc-daygrid-event {
            border-radius: 16px !important;
            font-size: 11.5px !important;
            font-weight: 500;
            padding: 2px 8px !important;
            border: none !important;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Overlap: nebeneinander in der Stundenansicht */
        .fc .fc-timegrid-event-harness {
            margin-right: 2px;
        }

        /* All-day row */
        .fc .fc-daygrid-body-natural .fc-daygrid-day-events {
            margin-bottom: 0;
        }

        .fc .fc-timegrid-col-events {
            margin: 0 2px;
        }

        /* Column headers */
        .fc .fc-col-header-cell {
            font-size: 0.8125rem;
            font-weight: 600;
            padding: 8px 4px;
        }

        /* Calendar min height for week/day view */
        .fc .fc-timegrid {
            min-height: 600px;
        }

        /* Detail Modal */
        #smart-event-detail-modal .modal-content {
            border-radius: 12px;
        }

        #smart-event-detail-modal .list-group-item {
            border: none;
            padding-top: 0.625rem;
            padding-bottom: 0.625rem;
        }

        #smart-event-detail-modal .list-group-item + .list-group-item {
            border-top: 1px solid rgba(0,0,0,0.06);
        }

        /* Legende unter dem Kalender */
        .calendar-legend {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            padding: 0.75rem 1rem;
            border-top: 1px solid rgba(0,0,0,0.06);
        }

        .calendar-legend-item {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            font-size: 0.8125rem;
            color: #6c757d;
        }

        .calendar-legend-chip {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }
    </style>
@endpush

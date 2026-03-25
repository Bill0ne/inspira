@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    {!! do_action('booking_reports_before_component_render') !!}

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
                    <label class="form-label" for="manual-booking-type">{{ trans('core/base::forms.type') }}</label>
                    <select class="form-select" id="manual-booking-type" name="type">
                        <option value="room">{{ trans('plugins/hotel::booking.room') }}</option>
                        <option value="course">{{ trans('plugins/courses::courses.course.name') }}</option>
                    </select>
                </div>
                <div class="col-lg-4" data-manual-booking-target="room">
                    <label class="form-label" for="manual-booking-room">{{ trans('plugins/hotel::booking.room') }}</label>
                    <select class="form-select" id="manual-booking-room" name="room_id">
                        <option value="">{{ trans('core/base::forms.select') }}</option>
                        @foreach ($rooms as $room)
                            <option value="{{ $room->id }}">{{ $room->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-4 d-none" data-manual-booking-target="course">
                    <label class="form-label" for="manual-booking-course">{{ trans('plugins/courses::courses.course.name') }}</label>
                    <select class="form-select" id="manual-booking-course" name="course_id">
                        <option value="">{{ trans('core/base::forms.select') }}</option>
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-6">
                    <label class="form-label" for="manual-booking-start">{{ trans('plugins/hotel::booking.start_date') }}</label>
                    <input class="form-control" type="datetime-local" id="manual-booking-start" name="start_at" required>
                </div>
                <div class="col-lg-6">
                    <label class="form-label" for="manual-booking-end">{{ trans('plugins/hotel::booking.end_date') }}</label>
                    <input class="form-control" type="datetime-local" id="manual-booking-end" name="end_at" required>
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
            const typeSelect = document.getElementById('manual-booking-type')
            const roomTarget = document.querySelector('[data-manual-booking-target="room"]')
            const courseTarget = document.querySelector('[data-manual-booking-target="course"]')

            const toggleTargets = () => {
                const isRoom = typeSelect.value === 'room'
                roomTarget.classList.toggle('d-none', !isRoom)
                courseTarget.classList.toggle('d-none', isRoom)
            }

            typeSelect?.addEventListener('change', toggleTargets)
            toggleTargets()
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

@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    {!! do_action('booking_reports_before_component_render') !!}

    <calendar-booking-reports-component
        v-cloak
        events-url="{{ route('booking.reports.records.index') }}"
    >
        <template v-slot:title>
            {{ trans('plugins/hotel::booking.calendar') }}
        </template>
        <template v-slot:actions>
            <x-core::button
                color="primary"
                data-bs-toggle="modal"
                data-bs-target="#manual-booking-modal"
            >
                {{ trans('plugins/hotel::booking.manual_booking') }}
            </x-core::button>
        </template>

        <template v-slot:event="{ booking }">
            <x-core::modal
                id="view-booking-event"
                type="info"
                v-if="booking"
                :title="trans('plugins/hotel::booking.name')"
                size="lg"
            >
                <div v-html="booking"></div>

                <x-slot name="footer">
                    <x-core::button data-bs-dismiss="modal">
                        {{ trans('core/base::forms.cancel') }}
                    </x-core::button>
                    <x-core::button
                        tag="a"
                        href="#"
                        target="_blank"
                        id="view-booking-event-link"
                        color="primary"
                    >
                        {{ trans('core/base::forms.edit') }}
                    </x-core::button>
                </x-slot>
            </x-core::modal>
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
@endpush

<script>
export default {
    props: {
        eventsUrl: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            calendarInstance: null,
            booking: null,
            loading: true,
        }
    },

    async mounted() {
        await this.$nextTick()

        if (this.calendarInstance) {
            calendarInstance.destroy()
        }

        if (this.$refs.calendar) {
            this.calendarInstance = new FullCalendar.Calendar(this.$refs.calendar, {
                fixedWeekCount: false,
                headerToolbar: {
                    left: 'title',
                },
                navLinks: true,
                editable: false,
                dayMaxEvents: true,
                events: {
                    url: this.eventsUrl,
                },
                loading: (isLoading) => {
                    this.loading = isLoading
                },
                eventClick: (info) => {
                    this.booking = info.event.extendedProps.detail
                    const detailUrl = info.event.extendedProps.detailUrl
                    const $link = $('#view-booking-event-link')
                    if (detailUrl) {
                        $link.attr('href', detailUrl).removeClass('d-none')
                    } else {
                        $link.addClass('d-none')
                    }
                    $('#view-booking-event').modal('show')
                },
            })

            this.calendarInstance.render()
        }
    },
}
</script>

<template>
    <div class="card">
        <div class="card-header">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 w-100">
                <h4 class="card-title mb-0">
                    <slot name="title"></slot>
                </h4>
                <div class="ms-auto">
                    <slot name="actions"></slot>
                </div>
            </div>
        </div>

        <div class="card-body" ref="calendar"></div>

        <slot name="loading" v-if="loading"></slot>

        <slot name="event" :booking="booking"></slot>
    </div>
</template>

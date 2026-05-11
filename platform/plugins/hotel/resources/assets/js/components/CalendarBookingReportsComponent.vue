<script>
export default {
    props: {
        eventsUrl: {
            type: String,
            required: true,
        },
        kpisUrl: {
            type: String,
            default: '',
        },
    },
    data() {
        return {
            calendarInstance: null,
            loading: true,
            showDetailModal: false,
            selectedEvent: null,
            kpis: null,
            kpisLoading: true,
        }
    },

    async mounted() {
        await this.$nextTick()

        if (this.calendarInstance) {
            this.calendarInstance.destroy()
        }

        if (this.$refs.calendar) {
            this.calendarInstance = new FullCalendar.Calendar(this.$refs.calendar, {
                initialView: 'timeGridWeek',
                fixedWeekCount: false,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay',
                },
                buttonText: {
                    today: 'Heute',
                    month: 'Monat',
                    week: 'Woche',
                    day: 'Tag',
                },
                locale: 'de',
                navLinks: true,
                editable: false,
                dayMaxEvents: true,
                nowIndicator: true,
                allDaySlot: true,
                allDayText: 'Ganztägig',
                slotMinTime: '06:00:00',
                slotMaxTime: '22:00:00',
                slotDuration: '00:30:00',
                slotLabelInterval: '01:00:00',
                slotLabelFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false,
                },
                slotEventOverlap: true,
                eventOverlap: true,
                expandRows: true,
                stickyHeaderDates: true,
                events: {
                    url: this.eventsUrl,
                },
                loading: (isLoading) => {
                    this.loading = isLoading
                },
                datesSet: (info) => {
                    // info.start / info.end = aktuell sichtbarer Bereich des Kalenders
                    this.fetchKpis(info?.start, info?.end)
                },
                eventClick: (info) => {
                    const props = info.event.extendedProps
                    this.selectedEvent = {
                        ...props,
                        title: info.event.title,
                        detailUrl: props.detailUrl,
                        detail: props.detail,
                        backgroundColor: info.event.backgroundColor,
                    }
                    this.showDetailModal = true
                    this.$nextTick(() => {
                        const modal = document.getElementById('smart-event-detail-modal')
                        if (modal) {
                            const bsModal = new bootstrap.Modal(modal)
                            bsModal.show()
                        }
                    })
                },
                eventDidMount: (info) => {
                    const el = info.el
                    const cardType = info.event.extendedProps.cardType

                    el.style.cursor = 'pointer'
                    el.style.borderRadius = '16px'
                    el.style.fontSize = '11.5px'
                    el.style.fontWeight = '500'
                    el.style.padding = '3px 10px'
                    el.style.border = 'none'
                    el.style.boxShadow = '0 1px 3px rgba(0,0,0,0.1)'
                    el.style.transition = 'transform 0.15s ease, box-shadow 0.15s ease'
                    el.style.overflow = 'hidden'

                    // Left border accent per type
                    if (cardType === 'room') {
                        el.style.borderLeft = '3px solid #0d6efd'
                    } else if (cardType === 'course') {
                        el.style.borderLeft = '3px solid #0563bb'
                    } else {
                        el.style.borderLeft = '3px solid #b8860b'
                    }

                    el.addEventListener('mouseenter', () => {
                        el.style.transform = 'translateY(-1px)'
                        el.style.boxShadow = '0 3px 8px rgba(0,0,0,0.15)'
                    })
                    el.addEventListener('mouseleave', () => {
                        el.style.transform = 'translateY(0)'
                        el.style.boxShadow = '0 1px 3px rgba(0,0,0,0.1)'
                    })
                },
            })

            this.calendarInstance.render()

            // Highlight today
            this.highlightToday()
        }
    },

    methods: {
        async fetchKpis(start, end) {
            if (!this.kpisUrl) return
            this.kpisLoading = true

            const toIsoDate = (value) => {
                if (! value) return null
                const date = value instanceof Date ? value : new Date(value)
                if (isNaN(date.getTime())) return null
                const y = date.getFullYear()
                const m = String(date.getMonth() + 1).padStart(2, '0')
                const d = String(date.getDate()).padStart(2, '0')
                return `${y}-${m}-${d}`
            }

            const params = new URLSearchParams()
            const startIso = toIsoDate(start)
            const endIso = toIsoDate(end)
            if (startIso) params.append('start', startIso)
            if (endIso) params.append('end', endIso)

            const url = params.toString()
                ? `${this.kpisUrl}?${params.toString()}`
                : this.kpisUrl

            try {
                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                })
                if (response.ok) {
                    this.kpis = await response.json()
                }
            } catch (e) {
                console.error('Failed to fetch KPIs', e)
            } finally {
                this.kpisLoading = false
            }
        },

        highlightToday() {
            this.$nextTick(() => {
                const todayCell = this.$refs.calendar?.querySelector('.fc-day-today')
                if (todayCell) {
                    todayCell.style.backgroundColor = 'rgba(var(--bb-primary-rgb, 100, 66, 34), 0.06)'
                }
            })
        },

        closeDetailModal() {
            this.showDetailModal = false
            this.selectedEvent = null
            const modal = document.getElementById('smart-event-detail-modal')
            if (modal) {
                const bsModal = bootstrap.Modal.getInstance(modal)
                if (bsModal) bsModal.hide()
            }
        },

        getEventIcon(cardType) {
            if (cardType === 'room') return '🏨'
            if (cardType === 'course') return '📚'
            return '📝'
        },
    },
}
</script>

<template>
    <div>
        <!-- KPI Cards -->
        <div class="row g-3 mb-4" v-if="kpisUrl">
            <div class="col-sm-6 col-xl-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <span class="avatar avatar-md rounded bg-primary-lt">🏨</span>
                            </div>
                            <div class="flex-grow-1 min-width-0">
                                <div class="text-secondary small">
                                    Raum-Auslastung
                                    <span v-if="kpis && kpis.range" class="text-muted">· {{ kpis.range.label }}</span>
                                </div>
                                <div class="d-flex align-items-baseline gap-2">
                                    <h3 class="mb-0" v-if="kpis">{{ kpis.room_occupancy.percent }}%</h3>
                                    <h3 class="mb-0" v-else>–</h3>
                                </div>
                                <div class="progress progress-sm mt-2" style="height: 4px;">
                                    <div
                                        class="progress-bar bg-primary"
                                        :style="{ width: (kpis ? kpis.room_occupancy.percent : 0) + '%' }"
                                    ></div>
                                </div>
                                <div class="text-secondary small mt-1" v-if="kpis">
                                    {{ kpis.room_occupancy.booked }} / {{ kpis.room_occupancy.available }} Raum-Tage
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <span class="avatar avatar-md rounded bg-info-lt">📚</span>
                            </div>
                            <div class="flex-grow-1 min-width-0">
                                <div class="text-secondary small">Kurs-Auslastung</div>
                                <div class="d-flex align-items-baseline gap-2">
                                    <h3 class="mb-0" v-if="kpis">{{ kpis.course_occupancy.percent }}%</h3>
                                    <h3 class="mb-0" v-else>–</h3>
                                </div>
                                <div class="progress progress-sm mt-2" style="height: 4px;">
                                    <div
                                        class="progress-bar bg-info"
                                        :style="{ width: (kpis ? kpis.course_occupancy.percent : 0) + '%' }"
                                    ></div>
                                </div>
                                <div class="text-secondary small mt-1" v-if="kpis">
                                    {{ kpis.course_occupancy.booked }} / {{ kpis.course_occupancy.available }} Plätze
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <span class="avatar avatar-md rounded" :class="kpis && kpis.overlaps.count > 0 ? 'bg-danger-lt' : 'bg-success-lt'">
                                    {{ kpis && kpis.overlaps.count > 0 ? '⚠️' : '✅' }}
                                </span>
                            </div>
                            <div class="flex-grow-1 min-width-0">
                                <div class="text-secondary small">Überschneidungen</div>
                                <div class="d-flex align-items-baseline gap-2">
                                    <h3 class="mb-0" v-if="kpis">{{ kpis.overlaps.count }}</h3>
                                    <h3 class="mb-0" v-else>–</h3>
                                </div>
                                <div class="text-secondary small mt-2" v-if="kpis">
                                    <span v-if="kpis.overlaps.count === 0" class="text-success">Keine Konflikte</span>
                                    <span v-else class="text-danger">Kurs & Raum am selben Raum</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <span class="avatar avatar-md rounded" :class="kpis && kpis.pending_bookings.total > 0 ? 'bg-warning-lt' : 'bg-success-lt'">
                                    {{ kpis && kpis.pending_bookings.total > 0 ? '⏳' : '✅' }}
                                </span>
                            </div>
                            <div class="flex-grow-1 min-width-0">
                                <div class="text-secondary small">Ausstehende Buchungen</div>
                                <div class="d-flex align-items-baseline gap-2">
                                    <h3 class="mb-0" v-if="kpis">{{ kpis.pending_bookings.total }}</h3>
                                    <h3 class="mb-0" v-else>–</h3>
                                </div>
                                <div class="text-secondary small mt-2" v-if="kpis">
                                    🏨 {{ kpis.pending_bookings.rooms }} Räume · 📚 {{ kpis.pending_bookings.courses }} Kurse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendar Card -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 w-100">
                    <h4 class="card-title mb-0">
                        <slot name="title"></slot>
                    </h4>
                    <div class="ms-auto d-flex gap-2 align-items-center">
                        <slot name="actions"></slot>
                    </div>
                </div>
            </div>

            <div class="card-body" ref="calendar"></div>

            <div class="calendar-legend">
                <span class="calendar-legend-item">
                    <span class="calendar-legend-chip" style="background: #0d6efd;"></span> 🏨 Raumbuchung
                </span>
                <span class="calendar-legend-item">
                    <span class="calendar-legend-chip" style="background: #ffc300;"></span> ⏳ Ausstehend
                </span>
                <span class="calendar-legend-item">
                    <span class="calendar-legend-chip" style="background: #36c6d3;"></span> ✅ Bestätigt
                </span>
                <span class="calendar-legend-item">
                    <span class="calendar-legend-chip" style="background: #9ecbff;"></span> 📚 Kurs-Session
                </span>
                <span class="calendar-legend-item">
                    <span class="calendar-legend-chip" style="background: #ffd966;"></span> 📝 Manuell
                </span>
            </div>

            <slot name="loading" v-if="loading"></slot>
        </div>

        <!-- Smart Detail Modal -->
        <div
            class="modal fade"
            id="smart-event-detail-modal"
            tabindex="-1"
            v-if="selectedEvent"
        >
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <!-- Card Header with Type Indicator -->
                    <div
                        class="modal-header border-0 pb-0"
                        :style="{ backgroundColor: selectedEvent.backgroundColor + '20' }"
                    >
                        <div class="d-flex align-items-center gap-2 w-100">
                            <span class="fs-2">{{ getEventIcon(selectedEvent.cardType) }}</span>
                            <div class="flex-grow-1 min-width-0">
                                <h4 class="modal-title mb-0 text-truncate">{{ selectedEvent.name }}</h4>
                                <span
                                    class="badge mt-1"
                                    :class="'bg-' + selectedEvent.statusColor + '-lt text-' + selectedEvent.statusColor"
                                >
                                    {{ selectedEvent.status }}
                                </span>
                            </div>
                            <button
                                type="button"
                                class="btn-close"
                                @click="closeDetailModal"
                            ></button>
                        </div>
                    </div>

                    <div class="modal-body pt-3">
                        <!-- Room Booking Card -->
                        <template v-if="selectedEvent.cardType === 'room'">
                            <div class="list-group list-group-flush">
                                <div class="list-group-item d-flex align-items-center px-0">
                                    <span class="me-3 text-secondary">📅</span>
                                    <div>
                                        <div class="text-secondary small">Zeitraum</div>
                                        <div>{{ selectedEvent.dateRange }}</div>
                                    </div>
                                </div>
                                <div class="list-group-item d-flex align-items-center px-0">
                                    <span class="me-3 text-secondary">👤</span>
                                    <div>
                                        <div class="text-secondary small">Gäste</div>
                                        <div>{{ selectedEvent.guests }} Erw.<template v-if="selectedEvent.children"> + {{ selectedEvent.children }} Kind.</template></div>
                                    </div>
                                </div>
                                <div class="list-group-item d-flex align-items-center px-0" v-if="selectedEvent.amount">
                                    <span class="me-3 text-secondary">💰</span>
                                    <div>
                                        <div class="text-secondary small">Betrag</div>
                                        <div>{{ selectedEvent.amount }}</div>
                                    </div>
                                </div>
                                <div class="list-group-item d-flex align-items-center px-0" v-if="selectedEvent.bookingNumber">
                                    <span class="me-3 text-secondary">🔖</span>
                                    <div>
                                        <div class="text-secondary small">Buchungsnr.</div>
                                        <div>{{ selectedEvent.bookingNumber }}</div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Course Card -->
                        <template v-if="selectedEvent.cardType === 'course'">
                            <div class="list-group list-group-flush">
                                <div class="list-group-item d-flex align-items-center px-0">
                                    <span class="me-3 text-secondary">📅</span>
                                    <div>
                                        <div class="text-secondary small">Datum & Zeit</div>
                                        <div>{{ selectedEvent.dateRange }}</div>
                                    </div>
                                </div>
                                <div class="list-group-item d-flex align-items-center px-0">
                                    <span class="me-3 text-secondary">👤</span>
                                    <div>
                                        <div class="text-secondary small">Plätze</div>
                                        <div>{{ selectedEvent.bookedSeats }} / {{ selectedEvent.availableSeats }} belegt</div>
                                    </div>
                                </div>
                                <div class="list-group-item d-flex align-items-center px-0" v-if="selectedEvent.room">
                                    <span class="me-3 text-secondary">🏠</span>
                                    <div>
                                        <div class="text-secondary small">Raum</div>
                                        <div>{{ selectedEvent.room }}</div>
                                    </div>
                                </div>
                                <div class="list-group-item d-flex align-items-center px-0" v-if="selectedEvent.instructor">
                                    <span class="me-3 text-secondary">👨‍🏫</span>
                                    <div>
                                        <div class="text-secondary small">Kursleiter</div>
                                        <div>{{ selectedEvent.instructor }}</div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Manual Booking Card -->
                        <template v-if="selectedEvent.cardType === 'manual'">
                            <div class="list-group list-group-flush">
                                <div class="list-group-item d-flex align-items-center px-0">
                                    <span class="me-3 text-secondary">📅</span>
                                    <div>
                                        <div class="text-secondary small">Zeitraum</div>
                                        <div>{{ selectedEvent.dateRange }}</div>
                                    </div>
                                </div>
                                <div class="list-group-item d-flex align-items-center px-0">
                                    <span class="me-3 text-secondary">📋</span>
                                    <div>
                                        <div class="text-secondary small">Typ</div>
                                        <div>{{ selectedEvent.type }}</div>
                                    </div>
                                </div>
                                <div class="list-group-item d-flex align-items-center px-0" v-if="selectedEvent.reason">
                                    <span class="me-3 text-secondary">💬</span>
                                    <div>
                                        <div class="text-secondary small">Grund</div>
                                        <div>{{ selectedEvent.reason }}</div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn" @click="closeDetailModal">
                            Schließen
                        </button>
                        <a
                            v-if="selectedEvent.detailUrl"
                            :href="selectedEvent.detailUrl"
                            class="btn btn-primary"
                            target="_blank"
                        >
                            Mehr Details →
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

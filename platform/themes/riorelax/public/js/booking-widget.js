const BookingWidget = (() => {
    const selectors = {
        slotCard: '[data-slot-card]',
        slotList: '[data-slot-list]',
        slotTemplate: 'template[data-slot-template]',
        slotValue: '[data-slot-value]',
        slotInput: '[data-slot-input]',
        slotLabel: '[data-slot-label]',
        addSlot: '[data-add-slot]',
        removeSlot: '[data-remove-slot]',
        error: '[data-error]',
        counter: '[data-counter]',
        counterButton: '[data-counter-action]',
        counterInput: 'input[type="number"]',
    }

    function pad(value) {
        return String(value).padStart(2, '0')
    }

    function parseDate(value) {
        if (typeof value !== 'string') {
            return null
        }

        const match = value.trim().match(/^(\d{2})\.(\d{2})\.(\d{4})$/)
        if (!match) {
            return null
        }

        const day = parseInt(match[1], 10)
        const month = parseInt(match[2], 10)
        const year = parseInt(match[3], 10)

        if (!Number.isFinite(day) || !Number.isFinite(month) || !Number.isFinite(year)) {
            return null
        }

        const date = new Date(year, month - 1, day)
        if (date.getFullYear() !== year || date.getMonth() !== month - 1 || date.getDate() !== day) {
            return null
        }

        return date
    }

    function parseTime(value) {
        if (typeof value !== 'string') {
            return null
        }

        const match = value.trim().match(/^(\d{1,2}):(\d{2})$/)
        if (!match) {
            return null
        }

        const hours = parseInt(match[1], 10)
        const minutes = parseInt(match[2], 10)

        if (
            !Number.isFinite(hours) ||
            !Number.isFinite(minutes) ||
            hours < 0 ||
            hours > 23 ||
            minutes < 0 ||
            minutes > 59
        ) {
            return null
        }

        return { hours, minutes }
    }

    function timeToMinutes(time) {
        return time.hours * 60 + time.minutes
    }

    function minutesToTimeString(totalMinutes) {
        const maxMinutes = 23 * 60 + 59
        const minutes = Math.min(Math.max(0, totalMinutes), maxMinutes)
        const hours = Math.floor(minutes / 60)
        const remainder = minutes % 60
        return `${pad(hours)}:${pad(remainder)}`
    }

    function ensureTimeWithinBounds(input, { min, max, increment }) {
        if (!input) {
            return
        }

        const step = Math.max(1, increment || 1)
        const minMinutes = typeof min === 'string' ? timeStringToMinutes(min) : null
        const maxMinutes = typeof max === 'string' ? timeStringToMinutes(max) : null
        let minutes = timeStringToMinutes((input.value || '').trim())

        if (minutes === null) {
            if (minMinutes !== null) {
                minutes = minMinutes
            } else {
                input.value = ''
                return
            }
        }

        if (minMinutes !== null && minutes < minMinutes) {
            minutes = minMinutes
        }

        if (maxMinutes !== null && minutes > maxMinutes) {
            minutes = maxMinutes
        }

        const remainder = minutes % step
        if (remainder !== 0) {
            minutes += step - remainder
        }

        if (maxMinutes !== null && minutes > maxMinutes) {
            minutes = maxMinutes
        }

        input.value = minutesToTimeString(minutes)
    }

    function combineDateTime(date, time) {
        if (!date || !time) {
            return null
        }

        return new Date(date.getFullYear(), date.getMonth(), date.getDate(), time.hours, time.minutes, 0, 0)
    }

    function normalizeDate(date) {
        return new Date(date.getFullYear(), date.getMonth(), date.getDate(), 0, 0, 0, 0)
    }

    function isSameDay(a, b) {
        return a.getFullYear() === b.getFullYear() && a.getMonth() === b.getMonth() && a.getDate() === b.getDate()
    }

    function roundToIncrement(date, increment) {
        const result = new Date(date.getTime())

        const hasRemainder = result.getMinutes() % increment !== 0
        const hasPartialMinute = result.getSeconds() !== 0 || result.getMilliseconds() !== 0

        result.setSeconds(0, 0)

        if (!hasRemainder && hasPartialMinute) {
            result.setMinutes(result.getMinutes() + increment)
            return result
        }

        if (hasRemainder) {
            const remainder = result.getMinutes() % increment
            result.setMinutes(result.getMinutes() + (increment - remainder))
        }

        return result
    }

    function getMinStartTimeForDate(dateValue, increment) {
        const selectedDate = parseDate(dateValue)
        if (!selectedDate) {
            return null
        }

        const today = new Date()
        const todayDate = normalizeDate(today)
        const selectedDay = normalizeDate(selectedDate)

        if (!isSameDay(selectedDay, todayDate)) {
            return '00:00'
        }

        const rounded = roundToIncrement(today, increment)
        if (!isSameDay(rounded, selectedDate)) {
            return '00:00'
        }

        return minutesToTimeString(rounded.getHours() * 60 + rounded.getMinutes())
    }

    function assignInputIds(widget) {
        const widgetId = widget.dataset.widgetId
        const cards = widget.querySelectorAll(selectors.slotCard)

        cards.forEach((card, index) => {
            card.dataset.index = index

            const inputs = card.querySelectorAll(selectors.slotInput)
            inputs.forEach((input) => {
                const field = input.dataset.slotInput
                if (!field) {
                    return
                }

                const id = `${widgetId}-slot-${index}-${field}`
                input.id = id
            })

            const labels = card.querySelectorAll(selectors.slotLabel)
            labels.forEach((label) => {
                const field = label.dataset.slotLabel
                if (!field) {
                    return
                }

                const input = card.querySelector(`[data-slot-input="${field}"]`)
                if (input) {
                    label.setAttribute('for', input.id)
                }
            })
        })
    }

    function updateSlotValue(card) {
        const dateInput = card.querySelector('[data-role="slot-date"]')
        const startInput = card.querySelector('[data-role="slot-start"]')
        const endInput = card.querySelector('[data-role="slot-end"]')
        const hidden = card.querySelector(selectors.slotValue)

        if (!hidden) {
            return
        }

        const dateValue = (dateInput?.value || '').trim()
        const startValue = (startInput?.value || '').trim()
        const endValue = (endInput?.value || '').trim()

        if (!dateValue || !startValue || !endValue) {
            hidden.value = ''
            return
        }

        hidden.value = `${dateValue} ${startValue} - ${endValue}`
    }

    function syncAllSlots(widget) {
        widget.querySelectorAll(selectors.slotCard).forEach(updateSlotValue)
    }

    function showError(widget, message) {
        const errorEl = widget.querySelector(selectors.error)
        if (!errorEl) {
            return
        }

        if (!message) {
            errorEl.hidden = true
            errorEl.textContent = ''
            return
        }

        errorEl.textContent = message
        errorEl.hidden = false
        errorEl.focus()
    }

    function validateSlots(widget, minDuration) {
        const cards = Array.from(widget.querySelectorAll(selectors.slotCard))

        if (!cards.length) {
            return { valid: false, message: widget.dataset.errorIncomplete || 'Please add at least one slot.' }
        }

        const parsedSlots = []
        const now = new Date()

        for (let index = 0; index < cards.length; index++) {
            const card = cards[index]
            const dateValue = (card.querySelector('[data-role="slot-date"]').value || '').trim()
            const startValue = (card.querySelector('[data-role="slot-start"]').value || '').trim()
            const endValue = (card.querySelector('[data-role="slot-end"]').value || '').trim()

            if (!dateValue || !startValue || !endValue) {
                return { valid: false, message: widget.dataset.errorIncomplete || 'Please complete all slot fields.' }
            }

            const date = parseDate(dateValue)
            const startTime = parseTime(startValue)
            const endTime = parseTime(endValue)

            if (!date || !startTime || !endTime) {
                return { valid: false, message: widget.dataset.errorInvalid || 'Please enter a valid date and time.' }
            }

            const start = combineDateTime(date, startTime)
            const end = combineDateTime(date, endTime)

            if (!start || !end || Number.isNaN(start.getTime()) || Number.isNaN(end.getTime())) {
                return { valid: false, message: widget.dataset.errorInvalid || 'Please enter a valid date and time.' }
            }

            if (end.getTime() <= start.getTime()) {
                return { valid: false, message: widget.dataset.errorInvalid || 'Please enter a valid date and time.' }
            }

            if (start.getTime() < now.getTime()) {
                return { valid: false, message: widget.dataset.errorPast || 'Slots must be in the future.' }
            }

            const duration = Math.round((end.getTime() - start.getTime()) / 60000)
            if (duration < minDuration) {
                return {
                    valid: false,
                    message: widget.dataset.errorDuration || `Slots must be at least ${minDuration} minutes.`,
                }
            }

            parsedSlots.push({ start, end })
        }

        const sorted = parsedSlots.slice().sort((a, b) => a.start.getTime() - b.start.getTime())

        for (let i = 1; i < sorted.length; i++) {
            if (sorted[i].start.getTime() < sorted[i - 1].end.getTime()) {
                return { valid: false, message: widget.dataset.errorOverlap || 'Slots cannot overlap.' }
            }
        }

        return { valid: true, message: '' }
    }

    function updateRemoveButtons(widget) {
        const cards = widget.querySelectorAll(selectors.slotCard)
        const disableRemove = cards.length <= 1

        cards.forEach((card) => {
            const remove = card.querySelector(selectors.removeSlot)
            if (!remove) {
                return
            }

            remove.disabled = disableRemove
            remove.setAttribute('aria-hidden', disableRemove ? 'true' : 'false')
        })
    }

    function timeStringToMinutes(value) {
        const time = parseTime(value)
        return time ? timeToMinutes(time) : null
    }

    function setupSlotCard(widget, card) {
        const minDuration = parseInt(widget.dataset.minDuration || '30', 10)
        const minuteIncrement = 30
        const dateInput = card.querySelector('[data-role="slot-date"]')
        const startInput = card.querySelector('[data-role="slot-start"]')
        const endInput = card.querySelector('[data-role="slot-end"]')

        function updateStartConstraints() {
            const minTime = getMinStartTimeForDate(dateInput?.value, minuteIncrement) || '00:00'
            ensureTimeWithinBounds(startInput, { min: minTime, max: '23:30', increment: minuteIncrement })
            updateEndConstraints()
        }

        function updateEndConstraints() {
            const startValue = (startInput?.value || '').trim()
            const startMinutes = startValue ? timeStringToMinutes(startValue) : null

            if (startMinutes === null) {
                const fallback = minutesToTimeString(minDuration)
                ensureTimeWithinBounds(endInput, { min: fallback, max: '23:59', increment: minuteIncrement })
                updateSlotValue(card)
                return
            }

            const minEndMinutes = Math.min(startMinutes + minDuration, 23 * 60 + 59)
            const minEndString = minutesToTimeString(minEndMinutes)
            ensureTimeWithinBounds(endInput, { min: minEndString, max: '23:59', increment: minuteIncrement })
            updateSlotValue(card)
        }

        if (dateInput) {
            dateInput.addEventListener('change', () => {
                updateStartConstraints()
                updateSlotValue(card)
            })
        }

        if (startInput) {
            const syncStart = () => {
                updateStartConstraints()
                updateSlotValue(card)
            }

            startInput.addEventListener('change', syncStart)
            startInput.addEventListener('input', syncStart)
        }

        if (endInput) {
            const syncEnd = () => {
                updateEndConstraints()
                updateSlotValue(card)
            }

            endInput.addEventListener('change', syncEnd)
            endInput.addEventListener('input', syncEnd)
        }

        updateStartConstraints()
        updateSlotValue(card)
    }

    function addSlot(widget) {
        const slotList = widget.querySelector(selectors.slotList)
        const template = widget.querySelector(selectors.slotTemplate)

        if (!slotList || !template) {
            return
        }

        const card = template.content.firstElementChild.cloneNode(true)
        slotList.appendChild(card)

        assignInputIds(widget)
        setupSlotCard(widget, card)
        updateRemoveButtons(widget)

        const dateInput = card.querySelector('[data-role="slot-date"]')
        if (dateInput) {
            dateInput.focus()
        }
    }

    function removeSlot(widget, card) {
        const slotList = widget.querySelector(selectors.slotList)
        if (!slotList || !card) {
            return
        }

        if (slotList.children.length <= 1) {
            return
        }

        card.remove()
        assignInputIds(widget)
        updateRemoveButtons(widget)
        syncAllSlots(widget)
    }

    function bindCounter(widget) {
        const counters = widget.querySelectorAll(selectors.counter)

        counters.forEach((counter) => {
            const input = counter.querySelector(selectors.counterInput)
            const buttons = counter.querySelectorAll(selectors.counterButton)

            buttons.forEach((button) => {
                button.addEventListener('click', () => {
                    if (!input) {
                        return
                    }

                    const action = button.dataset.counterAction
                    const min = parseInt(input.min || '0', 10)
                    const max = parseInt(input.max || '999', 10)
                    const current = parseInt(input.value || `${min}`, 10)

                    if (Number.isNaN(current)) {
                        input.value = `${min}`
                        return
                    }

                    if (action === 'increment') {
                        input.value = `${Math.min(current + 1, max)}`
                    } else if (action === 'decrement') {
                        input.value = `${Math.max(current - 1, min)}`
                    }
                })
            })
        })
    }

    function initWidget(widget) {
        const slotList = widget.querySelector(selectors.slotList)
        if (!slotList) {
            return
        }

        assignInputIds(widget)

        slotList.querySelectorAll(selectors.slotCard).forEach((card) => {
            setupSlotCard(widget, card)
        })

        updateRemoveButtons(widget)

        const addButton = widget.querySelector(selectors.addSlot)
        if (addButton) {
            addButton.addEventListener('click', () => addSlot(widget))
        }

        slotList.addEventListener('click', (event) => {
            const button = event.target.closest(selectors.removeSlot)
            if (!button) {
                return
            }

            const card = button.closest(selectors.slotCard)
            removeSlot(widget, card)
        })

        const form = widget.closest('form')
        if (form) {
            form.addEventListener('submit', (event) => {
                syncAllSlots(widget)

                const minDuration = parseInt(widget.dataset.minDuration || '30', 10)
                const validation = validateSlots(widget, minDuration)

                if (!validation.valid) {
                    event.preventDefault()
                    showError(widget, validation.message)
                    return
                }

                showError(widget, '')
            })
        }

        bindCounter(widget)
    }

    function startOnceLibrariesAvailable(attempt = 0) {
        const MAX_ATTEMPTS = 40
        const RETRY_DELAY = 100

        if (typeof window !== 'undefined' && typeof window.Litepicker === 'undefined') {
            if (attempt >= MAX_ATTEMPTS) {
                if (typeof console !== 'undefined' && console.warn) {
                    console.warn('Booking widget: Litepicker library not found.')
                }

                return
            }

            setTimeout(() => startOnceLibrariesAvailable(attempt + 1), RETRY_DELAY)
            return
        }

        document.querySelectorAll('[data-booking-widget]').forEach((widget) => {
            if (!widget.dataset.widgetId) {
                widget.dataset.widgetId = `booking-widget-${Math.random().toString(36).slice(2)}`
            }

            initWidget(widget)
        })
    }

    function init() {
        startOnceLibrariesAvailable()
    }

    return { init }
})()

document.addEventListener('DOMContentLoaded', () => {
    BookingWidget.init()
})

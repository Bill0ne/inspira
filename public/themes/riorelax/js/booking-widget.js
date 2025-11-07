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
        status: '[data-status]',
        statusMessage: '[data-status-message]',
        submit: '[data-submit]',
    }

    function pad(value) {
        return String(value).padStart(2, '0')
    }

    function parseInteger(value) {
        const parsed = parseInt(value ?? '', 10)
        return Number.isFinite(parsed) ? parsed : null
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

    function timeStringToMinutes(value) {
        const time = parseTime(value)
        return time ? timeToMinutes(time) : null
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

        if (minMinutes !== null && minutes < minMinutes) {
            minutes = minMinutes
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

    function parseDateTimeString(value) {
        if (typeof value !== 'string') {
            return null
        }

        const normalized = value.trim().replace(' ', 'T')
        if (!normalized) {
            return null
        }

        let candidate = normalized
        if (/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/.test(candidate)) {
            candidate = `${candidate}:00`
        }

        const date = new Date(candidate)
        if (Number.isNaN(date.getTime())) {
            return null
        }

        return date
    }

    function parseRangesFromDataset(value) {
        if (!value) {
            return []
        }

        let data
        try {
            data = JSON.parse(value)
        } catch (error) {
            return []
        }

        if (!Array.isArray(data)) {
            return []
        }

        return data
            .map((entry) => {
                const start = parseDateTimeString(entry?.start || entry?.from)
                const end = parseDateTimeString(entry?.end || entry?.to)

                if (!start || !end) {
                    return null
                }

                return {
                    start,
                    end,
                    title: typeof entry?.title === 'string' ? entry.title : '',
                }
            })
            .filter(Boolean)
    }

    function parseOperatingHours(value) {
        if (!value) {
            return { start: '09:30', end: '22:30' }
        }

        try {
            const parsed = JSON.parse(value)
            const start = typeof parsed?.start === 'string' ? parsed.start : '09:30'
            const end = typeof parsed?.end === 'string' ? parsed.end : '22:30'
            return { start, end }
        } catch (error) {
            return { start: '09:30', end: '22:30' }
        }
    }

    function parseWidgetConfig(widget) {
        if (widget._bookingConfig) {
            return widget._bookingConfig
        }

        const config = {
            bookings: parseRangesFromDataset(widget.dataset.bookedSlots),
            courseSessions: parseRangesFromDataset(widget.dataset.courseSessions),
            operatingHours: parseOperatingHours(widget.dataset.operatingHours),
            maxAdults: parseInteger(widget.dataset.maxAdults),
            maxParticipants: parseInteger(widget.dataset.maxParticipants),
        }

        widget._bookingConfig = config
        return config
    }

    function findOverlapRange(ranges, start, end) {
        if (!Array.isArray(ranges)) {
            return null
        }

        for (let index = 0; index < ranges.length; index++) {
            const range = ranges[index]
            if (start.getTime() < range.end.getTime() && end.getTime() > range.start.getTime()) {
                return range
            }
        }

        return null
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

    function getStatusTargets(widget) {
        const widgetId = widget.dataset.widgetId
        if (!widgetId) {
            return Array.from(widget.querySelectorAll(selectors.status))
        }

        return Array.from(document.querySelectorAll(`[data-status-for="${widgetId}"]`))
    }

    function updateStatus(widget, state) {
        const targets = getStatusTargets(widget)
        if (!targets.length) {
            return
        }

        const level = state?.level || 'info'
        const message = state?.message || ''

        targets.forEach((target) => {
            target.setAttribute('data-status-level', level)
            target.classList.remove('booking-widget__status--info', 'booking-widget__status--error', 'booking-widget__status--success')
            target.classList.add(`booking-widget__status--${level}`)

            const messageEl = target.querySelector(selectors.statusMessage)
            if (messageEl) {
                messageEl.textContent = message
            }
        })
    }

    function setSubmitState(widget, isEnabled) {
        const form = widget.closest('form')
        const buttons = form ? form.querySelectorAll(selectors.submit) : widget.querySelectorAll(selectors.submit)

        buttons.forEach((button) => {
            if (!(button instanceof HTMLButtonElement)) {
                return
            }

            button.disabled = !isEnabled
            button.setAttribute('aria-disabled', isEnabled ? 'false' : 'true')
            button.classList.toggle('is-disabled', !isEnabled)
        })
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

    function validateSlots(widget, minDuration, config) {
        const cards = Array.from(widget.querySelectorAll(selectors.slotCard))

        if (!cards.length) {
            return {
                valid: false,
                message: widget.dataset.statusInitial || widget.dataset.errorIncomplete || 'Bitte fügen Sie mindestens einen Slot hinzu.',
                level: 'info',
            }
        }

        const parsedSlots = []
        const now = new Date()
        const allowedStart = timeStringToMinutes(config?.operatingHours?.start || '09:30')
        const allowedEnd = timeStringToMinutes(config?.operatingHours?.end || '22:30')

        const form = widget.closest('form')
        const adultsInput = form ? form.querySelector('input[name="adults"]') : widget.querySelector('input[name="adults"]')
        const childrenInput = form ? form.querySelector('input[name="children"]') : widget.querySelector('input[name="children"]')
        const adultsValue = adultsInput ? parseInteger(adultsInput.value) : null
        const childrenValue = childrenInput ? parseInteger(childrenInput.value) : 0

        for (let index = 0; index < cards.length; index++) {
            const card = cards[index]
            const dateValue = (card.querySelector('[data-role="slot-date"]').value || '').trim()
            const startValue = (card.querySelector('[data-role="slot-start"]').value || '').trim()
            const endValue = (card.querySelector('[data-role="slot-end"]').value || '').trim()

            if (!dateValue || !startValue || !endValue) {
                return {
                    valid: false,
                    message: widget.dataset.errorIncomplete || widget.dataset.statusInitial || 'Bitte füllen Sie alle Slot-Felder aus.',
                    level: 'info',
                }
            }

            const date = parseDate(dateValue)
            const startTime = parseTime(startValue)
            const endTime = parseTime(endValue)

            if (!date || !startTime || !endTime) {
                return {
                    valid: false,
                    message: widget.dataset.errorInvalid || 'Bitte geben Sie ein gültiges Datum und eine gültige Uhrzeit ein.',
                    level: 'error',
                }
            }

            const start = combineDateTime(date, startTime)
            const end = combineDateTime(date, endTime)

            if (!start || !end || Number.isNaN(start.getTime()) || Number.isNaN(end.getTime()) || end.getTime() <= start.getTime()) {
                return {
                    valid: false,
                    message: widget.dataset.errorInvalid || 'Bitte geben Sie ein gültiges Datum und eine gültige Uhrzeit ein.',
                    level: 'error',
                }
            }

            if (start.getTime() < now.getTime()) {
                return {
                    valid: false,
                    message: widget.dataset.errorPast || 'Slots müssen in der Zukunft liegen.',
                    level: 'error',
                }
            }

            const startMinutes = timeStringToMinutes(startValue)
            const endMinutes = timeStringToMinutes(endValue)

            if (allowedStart !== null && startMinutes !== null && startMinutes < allowedStart) {
                return {
                    valid: false,
                    message: widget.dataset.errorHours || 'Buchungen sind nur zwischen den erlaubten Zeiten möglich.',
                    level: 'error',
                }
            }

            if (allowedEnd !== null && endMinutes !== null && endMinutes > allowedEnd) {
                return {
                    valid: false,
                    message: widget.dataset.errorHours || 'Buchungen sind nur zwischen den erlaubten Zeiten möglich.',
                    level: 'error',
                }
            }

            const duration = Math.round((end.getTime() - start.getTime()) / 60000)
            if (duration < minDuration) {
                return {
                    valid: false,
                    message: widget.dataset.errorDuration || `Slots müssen mindestens ${minDuration} Minuten umfassen.`,
                    level: 'error',
                }
            }

            const bookingConflict = findOverlapRange(config?.bookings, start, end)
            if (bookingConflict) {
                return {
                    valid: false,
                    message: widget.dataset.errorBooked || 'Der Raum ist im ausgewählten Zeitraum bereits reserviert.',
                    level: 'error',
                }
            }

            const courseConflict = findOverlapRange(config?.courseSessions, start, end)
            if (courseConflict) {
                const baseMessage = widget.dataset.errorCourse || 'In diesem Zeitraum findet bereits ein Kurs statt.'
                const name = courseConflict.title ? ` (${courseConflict.title})` : ''

                return {
                    valid: false,
                    message: `${baseMessage}${name}`,
                    level: 'error',
                }
            }

            parsedSlots.push({ start, end })
        }

        const sorted = parsedSlots.slice().sort((a, b) => a.start.getTime() - b.start.getTime())

        for (let i = 1; i < sorted.length; i++) {
            if (sorted[i].start.getTime() < sorted[i - 1].end.getTime()) {
                return {
                    valid: false,
                    message: widget.dataset.errorOverlap || 'Slots dürfen sich nicht überschneiden.',
                    level: 'error',
                }
            }
        }

        if (config?.maxAdults !== null && adultsValue !== null && adultsValue > config.maxAdults) {
            return {
                valid: false,
                message: widget.dataset.errorCapacity || 'Die Anzahl der Erwachsenen überschreitet die maximale Kapazität.',
                level: 'error',
            }
        }

        if (config?.maxParticipants !== null && adultsValue !== null) {
            const totalParticipants = adultsValue + (Number.isFinite(childrenValue) ? childrenValue : 0)
            if (totalParticipants > config.maxParticipants) {
                return {
                    valid: false,
                    message: widget.dataset.errorParticipants || widget.dataset.errorCapacity || 'Die Gesamtzahl der Teilnehmenden überschreitet die maximale Kapazität.',
                    level: 'error',
                }
            }
        }

        return {
            valid: true,
            message: widget.dataset.statusReady || 'Alle Angaben sehen gut aus.',
            level: 'success',
        }
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

    function setupSlotCard(widget, card, config) {
        const minDuration = parseInt(widget.dataset.minDuration || '30', 10)
        const minuteIncrement = 30
        const dateInput = card.querySelector('[data-role="slot-date"]')
        const startInput = card.querySelector('[data-role="slot-start"]')
        const endInput = card.querySelector('[data-role="slot-end"]')
        const operatingStart = config?.operatingHours?.start || '09:30'
        const operatingEnd = config?.operatingHours?.end || '22:30'
        const operatingStartMinutes = timeStringToMinutes(operatingStart) ?? 0
        const operatingEndMinutes = timeStringToMinutes(operatingEnd) ?? 23 * 60 + 59

        function updateEndConstraints() {
            const startValue = (startInput?.value || '').trim()
            const startMinutes = startValue ? timeStringToMinutes(startValue) : null

            let minEndMinutes = operatingStartMinutes + minDuration
            if (startMinutes !== null) {
                minEndMinutes = Math.max(startMinutes + minDuration, minEndMinutes)
            }

            const boundedMinEnd = Math.min(minEndMinutes, operatingEndMinutes)
            const minEndString = minutesToTimeString(boundedMinEnd)

            ensureTimeWithinBounds(endInput, { min: minEndString, max: operatingEnd, increment: minuteIncrement })
            updateSlotValue(card)
        }

        function updateStartConstraints() {
            const minTime = getMinStartTimeForDate(dateInput?.value, minuteIncrement)
            const minTimeMinutes = minTime ? timeStringToMinutes(minTime) : null
            const maxMinutes = operatingEndMinutes - minDuration
            let minMinutes = operatingStartMinutes

            if (minTimeMinutes !== null) {
                minMinutes = Math.max(minMinutes, minTimeMinutes)
            }

            const boundedMin = Math.min(minMinutes, maxMinutes)
            const minString = minutesToTimeString(boundedMin)

            ensureTimeWithinBounds(startInput, { min: minString, max: operatingEnd, increment: minuteIncrement })
            updateEndConstraints()
        }

        if (dateInput) {
            dateInput.addEventListener('change', () => {
                updateStartConstraints()
                updateSlotValue(card)
                refreshWidgetState(widget)
            })
        }

        if (startInput) {
            const syncStart = () => {
                updateStartConstraints()
                updateSlotValue(card)
                refreshWidgetState(widget)
            }

            startInput.addEventListener('change', syncStart)
            startInput.addEventListener('input', syncStart)
        }

        if (endInput) {
            const syncEnd = () => {
                updateEndConstraints()
                updateSlotValue(card)
                refreshWidgetState(widget)
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
        setupSlotCard(widget, card, parseWidgetConfig(widget))
        updateRemoveButtons(widget)
        refreshWidgetState(widget)

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
        refreshWidgetState(widget)
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
                        refreshWidgetState(widget)
                        return
                    }

                    if (action === 'increment') {
                        input.value = `${Math.min(current + 1, max)}`
                    } else if (action === 'decrement') {
                        input.value = `${Math.max(current - 1, min)}`
                    }

                    refreshWidgetState(widget)
                })
            })

            if (input) {
                input.addEventListener('change', () => refreshWidgetState(widget))
                input.addEventListener('input', () => refreshWidgetState(widget))
            }
        })
    }

    function refreshWidgetState(widget) {
        if (!widget) {
            return { valid: false, message: '', level: 'info' }
        }

        syncAllSlots(widget)
        const minDuration = parseInt(widget.dataset.minDuration || '30', 10)
        const validation = validateSlots(widget, minDuration, parseWidgetConfig(widget))
        const initialMessage = widget.dataset.statusInitial || ''
        const message = validation.message || (validation.valid ? widget.dataset.statusReady || initialMessage : initialMessage)
        const level = validation.level || (validation.valid ? 'success' : 'info')

        updateStatus(widget, { level, message })
        showError(widget, validation.level === 'error' ? validation.message : '')
        setSubmitState(widget, validation.valid)

        return validation
    }

    function initWidget(widget) {
        const slotList = widget.querySelector(selectors.slotList)
        if (!slotList) {
            return
        }

        parseWidgetConfig(widget)
        assignInputIds(widget)

        slotList.querySelectorAll(selectors.slotCard).forEach((card) => {
            setupSlotCard(widget, card, parseWidgetConfig(widget))
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
                const validation = refreshWidgetState(widget)

                if (!validation.valid) {
                    event.preventDefault()
                    return
                }

                showError(widget, '')
            })
        }

        bindCounter(widget)
        refreshWidgetState(widget)
    }

    function startOnceLibrariesAvailable(attempt = 0) {
        const MAX_ATTEMPTS = 40
        const RETRY_DELAY = 100

        if (typeof window !== 'undefined' && typeof window.Litepicker === 'undefined') {
            if (attempt >= MAX_ATTEMPTS) {
                if (typeof console !== 'undefined' && console.warn) {
                    console.warn('Buchungs-Widget: Litepicker-Bibliothek nicht gefunden.')
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

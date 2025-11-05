const BookingWidget = (() => {
    const DATE_FORMAT = 'DD.MM.YYYY'
    const TIME_FORMAT = 'HH:mm'
    const DATE_TIME_FORMAT = `${DATE_FORMAT} ${TIME_FORMAT}`

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

    function roundToIncrement(date, increment) {
        const minutes = date.minute()
        const remainder = minutes % increment

        if (remainder === 0) {
            return date.second(0)
        }

        return date.add(increment - remainder, 'minute').second(0)
    }

    function getMinStartTimeForDate(dateValue, increment) {
        const today = dayjs()
        const selectedDate = dayjs(dateValue, DATE_FORMAT, true)

        if (!selectedDate.isValid()) {
            return null
        }

        if (selectedDate.isAfter(today, 'day')) {
            return '00:00'
        }

        if (!selectedDate.isSame(today, 'day')) {
            return '00:00'
        }

        const rounded = roundToIncrement(today, increment)
        return rounded.format(TIME_FORMAT)
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

        for (let index = 0; index < cards.length; index++) {
            const card = cards[index]
            const dateValue = card.querySelector('[data-role="slot-date"]').value.trim()
            const startValue = card.querySelector('[data-role="slot-start"]').value.trim()
            const endValue = card.querySelector('[data-role="slot-end"]').value.trim()

            if (!dateValue || !startValue || !endValue) {
                return { valid: false, message: widget.dataset.errorIncomplete || 'Please complete all slot fields.' }
            }

            const start = dayjs(`${dateValue} ${startValue}`, DATE_TIME_FORMAT, true)
            const end = dayjs(`${dateValue} ${endValue}`, DATE_TIME_FORMAT, true)

            if (!start.isValid() || !end.isValid()) {
                return { valid: false, message: widget.dataset.errorInvalid || 'Please enter a valid date and time.' }
            }

            const now = dayjs()
            if (start.isBefore(now)) {
                return { valid: false, message: widget.dataset.errorPast || 'Slots must be in the future.' }
            }

            const duration = end.diff(start, 'minute')
            if (duration < minDuration) {
                return {
                    valid: false,
                    message: widget.dataset.errorDuration || `Slots must be at least ${minDuration} minutes.`,
                }
            }

            parsedSlots.push({ index, start, end })
        }

        const sorted = parsedSlots.slice().sort((a, b) => a.start.valueOf() - b.start.valueOf())

        for (let i = 1; i < sorted.length; i++) {
            if (sorted[i].start.isBefore(sorted[i - 1].end)) {
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

    function createFlatpickrInstance(input, options) {
        if (!input) {
            return null
        }

        if (input._flatpickr) {
            input._flatpickr.destroy()
        }

        return flatpickr(input, options)
    }

    function setupSlotCard(widget, card) {
        const minDuration = parseInt(widget.dataset.minDuration || '30', 10)
        const minuteIncrement = 30
        const dateInput = card.querySelector('[data-role="slot-date"]')
        const startInput = card.querySelector('[data-role="slot-start"]')
        const endInput = card.querySelector('[data-role="slot-end"]')

        createFlatpickrInstance(dateInput, {
            dateFormat: 'd.m.Y',
            minDate: 'today',
            locale: flatpickr.l10ns?.de ?? 'de',
            disableMobile: true,
            allowInput: true,
            onChange: () => {
                updateStartConstraints()
                updateSlotValue(card)
            },
        })

        const startPicker = createFlatpickrInstance(startInput, {
            enableTime: true,
            noCalendar: true,
            dateFormat: 'H:i',
            time_24hr: true,
            minuteIncrement,
            minTime: '00:00',
            maxTime: '23:30',
            allowInput: true,
            disableMobile: true,
            locale: flatpickr.l10ns?.de ?? 'de',
            onValueUpdate: () => {
                updateEndConstraints()
                updateSlotValue(card)
            },
            onClose: () => {
                updateEndConstraints()
                updateSlotValue(card)
            },
        })

        const endPicker = createFlatpickrInstance(endInput, {
            enableTime: true,
            noCalendar: true,
            dateFormat: 'H:i',
            time_24hr: true,
            minuteIncrement,
            minTime: '00:30',
            maxTime: '23:59',
            allowInput: true,
            disableMobile: true,
            locale: flatpickr.l10ns?.de ?? 'de',
            onValueUpdate: () => updateSlotValue(card),
            onClose: () => updateSlotValue(card),
        })

        function updateStartConstraints() {
            const minTime = getMinStartTimeForDate(dateInput?.value, minuteIncrement)

            if (startPicker && minTime) {
                startPicker.set('minTime', minTime)

                if (startInput.value) {
                    const current = dayjs(startInput.value, TIME_FORMAT, true)
                    const minTimeValue = dayjs(minTime, TIME_FORMAT, true)

                    if (current.isValid() && current.isBefore(minTimeValue)) {
                        startPicker.setDate(minTime, true, 'H:i')
                    }
                }
            } else if (startPicker) {
                startPicker.set('minTime', '00:00')
            }

            updateEndConstraints()
        }

        function updateEndConstraints() {
            if (!startInput || !endPicker) {
                return
            }

            const startValue = startInput.value.trim()
            if (!startValue) {
                endPicker.set('minTime', '00:30')
                return
            }

            const startTime = dayjs(startValue, TIME_FORMAT, true)
            if (!startTime.isValid()) {
                return
            }

            const minEnd = startTime.add(minDuration, 'minute')
            const minEndString = minEnd.format(TIME_FORMAT)
            endPicker.set('minTime', minEndString)

            const endValue = endInput.value.trim()
            if (!endValue) {
                endPicker.setDate(minEndString, true, 'H:i')
                return
            }

            const endTime = dayjs(endValue, TIME_FORMAT, true)
            if (!endTime.isValid() || endTime.isBefore(minEnd)) {
                endPicker.setDate(minEndString, true, 'H:i')
            }
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
            if (!input) {
                return
            }

            counter.addEventListener('click', (event) => {
                const button = event.target.closest(selectors.counterButton)
                if (!button) {
                    return
                }

                event.preventDefault()

                const action = button.dataset.counterAction
                const min = parseInt(input.min || '0', 10)
                const max = parseInt(input.max || '999', 10)
                let value = parseInt(input.value || String(min || 0), 10)

                if (Number.isNaN(value)) {
                    value = min
                }

                if (action === 'decrement' && value > min) {
                    input.value = String(value - 1)
                }

                if (action === 'increment' && value < max) {
                    input.value = String(value + 1)
                }
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

        if (typeof flatpickr === 'undefined' || typeof dayjs === 'undefined') {
            if (attempt >= MAX_ATTEMPTS) {
                if (typeof console !== 'undefined' && console.warn) {
                    console.warn('Booking widget: flatpickr/dayjs libraries not found.')
                }

                return
            }

            setTimeout(() => startOnceLibrariesAvailable(attempt + 1), RETRY_DELAY)
            return
        }

        if (window.dayjs_plugin_customParseFormat) {
            dayjs.extend(window.dayjs_plugin_customParseFormat)
        }

        if (dayjs.locale) {
            dayjs.locale(document.documentElement.lang || 'de')
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

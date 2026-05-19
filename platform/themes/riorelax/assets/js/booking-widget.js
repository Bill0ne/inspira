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
        slotDateField: '[data-slot-date-field]',
        slotStartField: '[data-slot-start-field]',
        slotEndField: '[data-slot-end-field]',
    }

    function pad(value) {
        return String(value).padStart(2, '0')
    }

    function parseDate(value) {
        if (typeof value !== 'string') return null
        const match = value.trim().match(/^(\d{2})\.(\d{2})\.(\d{4})$/)
        if (!match) return null

        const day = parseInt(match[1], 10)
        const month = parseInt(match[2], 10)
        const year = parseInt(match[3], 10)

        const date = new Date(year, month - 1, day)
        if (
            date.getFullYear() !== year ||
            date.getMonth() !== month - 1 ||
            date.getDate() !== day
        ) return null

        return date
    }

    function parseTime(value) {
        if (typeof value !== 'string') return null
        const match = value.trim().match(/^(\d{1,2}):(\d{2})$/)
        if (!match) return null

        const hours = parseInt(match[1], 10)
        const minutes = parseInt(match[2], 10)

        if (
            hours < 0 || hours > 23 ||
            minutes < 0 || minutes > 59
        ) return null

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
        if (!input) return

        const step = Math.max(1, increment || 1)
        const minMinutes = min ? timeStringToMinutes(min) : null
        const maxMinutes = max ? timeStringToMinutes(max) : null

        let minutes = timeStringToMinutes(input.value.trim())
        if (minutes === null) {
            input.value = minMinutes !== null
                ? minutesToTimeString(minMinutes)
                : ''
            return
        }

        if (minMinutes !== null && minutes < minMinutes) minutes = minMinutes
        if (maxMinutes !== null && minutes > maxMinutes) minutes = maxMinutes

        const remainder = minutes % step
        if (remainder !== 0) minutes += (step - remainder)

        if (maxMinutes !== null && minutes > maxMinutes) minutes = maxMinutes

        input.value = minutesToTimeString(minutes)
    }

    function combineDateTime(date, time) {
        if (!date || !time) return null
        return new Date(
            date.getFullYear(),
            date.getMonth(),
            date.getDate(),
            time.hours,
            time.minutes,
            0,
            0
        )
    }

    function normalizeDate(date) {
        return new Date(date.getFullYear(), date.getMonth(), date.getDate(), 0, 0, 0, 0)
    }

    function isSameDay(a, b) {
        return (
            a.getFullYear() === b.getFullYear() &&
            a.getMonth() === b.getMonth() &&
            a.getDate() === b.getDate()
        )
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

    function getOpeningWindow(widget) {
        const start = (widget && widget.dataset.openingStart) || '10:00'
        const end = (widget && widget.dataset.openingEnd) || '21:00'
        const startMinutes = timeStringToMinutes(start)
        const endMinutes = timeStringToMinutes(end)

        return {
            start,
            end,
            startMinutes: startMinutes !== null ? startMinutes : 10 * 60,
            endMinutes: endMinutes !== null ? endMinutes : 21 * 60,
        }
    }

    function getMinStartTimeForDate(dateValue, increment, openingStart) {
        const fallback = openingStart || '00:00'
        const selectedDate = parseDate(dateValue)
        if (!selectedDate) return fallback

        const today = new Date()
        const todayDate = normalizeDate(today)
        const selectedDay = normalizeDate(selectedDate)

        if (!isSameDay(selectedDay, todayDate)) return fallback

        const rounded = roundToIncrement(today, increment)
        if (!isSameDay(rounded, selectedDate)) return fallback

        const nowMinutes = rounded.getHours() * 60 + rounded.getMinutes()
        const openingMinutes = timeStringToMinutes(fallback) ?? 0

        return minutesToTimeString(Math.max(nowMinutes, openingMinutes))
    }

    function assignInputIds(widget) {
        const widgetId = widget.dataset.widgetId
        const cards = widget.querySelectorAll(selectors.slotCard)

        cards.forEach((card, index) => {
            card.dataset.index = index

            const inputs = card.querySelectorAll(selectors.slotInput)
            inputs.forEach((input) => {
                const field = input.dataset.slotInput
                if (!field) return

                const id = `${widgetId}-slot-${index}-${field}`
                input.id = id
            })

            const labels = card.querySelectorAll(selectors.slotLabel)
            labels.forEach((label) => {
                const field = label.dataset.slotLabel
                if (!field) return

                const input = card.querySelector(`[data-slot-input="${field}"]`)
                if (input) label.setAttribute('for', input.id)
            })
        })
    }

    // ⭐ FINAL VERSION: Updated to sync raw + date + start + end
    function updateSlotValue(card) {
        const dateInput = card.querySelector('[data-role="slot-date"]');
        const startInput = card.querySelector('[data-role="slot-start"]');
        const endInput = card.querySelector('[data-role="slot-end"]');

        const rawField = card.querySelector(selectors.slotValue);
        const dateField = card.querySelector(selectors.slotDateField);
        const startField = card.querySelector(selectors.slotStartField);
        const endField = card.querySelector(selectors.slotEndField);

        const dateValue = (dateInput?.value || '').trim();
        const startValue = (startInput?.value || '').trim();
        const endValue = (endInput?.value || '').trim();

        if (!dateValue || !startValue || !endValue) {
            if (rawField) rawField.value = '';
            if (dateField) dateField.value = '';
            if (startField) startField.value = '';
            if (endField) endField.value = '';
            return;
        }

        // RAW Feld aktualisieren
        if (rawField) rawField.value = `${dateValue} ${startValue} - ${endValue}`;

        // Strukturierte Felder aktualisieren
        if (dateField) dateField.value = dateValue;
        if (startField) startField.value = startValue;
        if (endField) endField.value = endValue;
    }

    function syncAllSlots(widget) {
        widget.querySelectorAll(selectors.slotCard).forEach(updateSlotValue)
    }

    function showError(widget, message) {
        const errorEl = widget.querySelector(selectors.error)
        if (!errorEl) return

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
            return { valid: false, message: widget.dataset.errorIncomplete || 'Bitte füge mindestens einen Slot hinzu.' }
        }

        const parsedSlots = []
        const now = new Date()
        const opening = getOpeningWindow(widget)
        const openingMessage = widget.dataset.errorOpening
            || `Buchungen sind nur zwischen ${opening.start} und ${opening.end} Uhr möglich.`

        for (let index = 0; index < cards.length; index++) {
            const card = cards[index]
            const dateValue = (card.querySelector('[data-role="slot-date"]').value || '').trim()
            const startValue = (card.querySelector('[data-role="slot-start"]').value || '').trim()
            const endValue = (card.querySelector('[data-role="slot-end"]').value || '').trim()

            if (!dateValue || !startValue || !endValue) {
                return { valid: false, message: widget.dataset.errorIncomplete || 'Bitte alle Slot-Felder ausfüllen.' }
            }

            const date = parseDate(dateValue)
            const startTime = parseTime(startValue)
            const endTime = parseTime(endValue)

            if (!date || !startTime || !endTime) {
                return { valid: false, message: widget.dataset.errorInvalid || 'Bitte gültiges Datum und gültige Uhrzeit eingeben.' }
            }

            const startMinutesOfDay = timeToMinutes(startTime)
            const endMinutesOfDay = timeToMinutes(endTime)

            if (startMinutesOfDay < opening.startMinutes || endMinutesOfDay > opening.endMinutes) {
                return { valid: false, message: openingMessage }
            }

            const start = combineDateTime(date, startTime)
            const end = combineDateTime(date, endTime)

            if (end.getTime() <= start.getTime()) {
                return { valid: false, message: widget.dataset.errorInvalid || 'Ende muss nach dem Beginn liegen.' }
            }

            if (start.getTime() < now.getTime()) {
                return { valid: false, message: widget.dataset.errorPast || 'Slot darf nicht in der Vergangenheit liegen.' }
            }

            const duration = Math.round((end.getTime() - start.getTime()) / 60000)
            if (duration < minDuration) {
                return { valid: false, message: widget.dataset.errorDuration || 'Slot ist zu kurz.' }
            }

            parsedSlots.push({ start, end })
        }

        // Überlappungsprüfung
        parsedSlots
            .sort((a, b) => a.start - b.start)
            .reduce((prev, current) => {
                if (prev && prev.end > current.start) {
                    throw { valid: false, message: widget.dataset.errorOverlap || 'Slots dürfen sich nicht überschneiden.' }
                }
                return current
            }, null)

        return { valid: true }
    }

    function updateRemoveButtons(widget) {
        const cards = widget.querySelectorAll(selectors.slotCard)
        const disableRemove = cards.length <= 1

        cards.forEach((card) => {
            const removeBtn = card.querySelector(selectors.removeSlot)
            if (!removeBtn) return

            removeBtn.disabled = disableRemove
            removeBtn.setAttribute('aria-hidden', disableRemove)
        })
    }

    function setupSlotCard(widget, card) {
        const minDuration = parseInt(widget.dataset.minDuration || '30', 10)
        const increment = 30
        const opening = getOpeningWindow(widget)
        const latestStartMinutes = Math.max(opening.startMinutes, opening.endMinutes - minDuration)
        const latestStart = minutesToTimeString(latestStartMinutes)

        const dateInput = card.querySelector('[data-role="slot-date"]')
        const startInput = card.querySelector('[data-role="slot-start"]')
        const endInput = card.querySelector('[data-role="slot-end"]')

        function updateStart() {
            const minStart = getMinStartTimeForDate(dateInput?.value, increment, opening.start) || opening.start
            ensureTimeWithinBounds(startInput, { min: minStart, max: latestStart, increment })
            updateEnd()
        }

        function updateEnd() {
            const startValue = startInput?.value.trim() || ''
            const startMinutes = timeStringToMinutes(startValue)
            let minEnd = opening.start

            if (startMinutes !== null) {
                minEnd = minutesToTimeString(startMinutes + minDuration)
            }

            ensureTimeWithinBounds(endInput, { min: minEnd, max: opening.end, increment })
            updateSlotValue(card)
        }

        if (dateInput) dateInput.addEventListener('change', () => {
            updateStart()
            updateSlotValue(card)
        })

        if (startInput) {
            const fn = () => {
                updateStart()
                updateSlotValue(card)
            }
            startInput.addEventListener('input', fn)
            startInput.addEventListener('change', fn)
        }

        if (endInput) {
            const fn = () => {
                updateEnd()
                updateSlotValue(card)
            }
            endInput.addEventListener('input', fn)
            endInput.addEventListener('change', fn)
        }

        updateStart()
        updateSlotValue(card)
    }

    function addSlot(widget) {
        const list = widget.querySelector(selectors.slotList)
        const template = widget.querySelector(selectors.slotTemplate)
        const card = template.content.firstElementChild.cloneNode(true)

        list.appendChild(card)
        assignInputIds(widget)
        setupSlotCard(widget, card)
        updateRemoveButtons(widget)

        const dateInput = card.querySelector('[data-role="slot-date"]')
        if (dateInput) dateInput.focus()
    }

    function removeSlot(widget, card) {
        const list = widget.querySelector(selectors.slotList)
        if (list.children.length <= 1) return

        card.remove()
        assignInputIds(widget)
        updateRemoveButtons(widget)
        syncAllSlots(widget)
    }

    function bindCounter(widget) {
        const counters = widget.querySelectorAll(selectors.counter)

        counters.forEach((counter) => {
            const input = counter.querySelector(selectors.counterInput)
            const btns = counter.querySelectorAll(selectors.counterButton)

            btns.forEach((btn) => {
                btn.addEventListener('click', () => {
                    const action = btn.dataset.counterAction
                    const min = parseInt(input.min || '0', 10)
                    const max = parseInt(input.max || '999', 10)
                    const value = parseInt(input.value || `${min}`, 10)

                    if (action === 'increment') {
                        input.value = Math.min(value + 1, max)
                    } else {
                        input.value = Math.max(value - 1, min)
                    }
                })
            })
        })
    }

    function initWidget(widget) {
        const slotList = widget.querySelector(selectors.slotList)
        if (!slotList) return

        assignInputIds(widget)

        slotList.querySelectorAll(selectors.slotCard).forEach((card) => {
            setupSlotCard(widget, card)
        })

        updateRemoveButtons(widget)

        const addBtn = widget.querySelector(selectors.addSlot)
        if (addBtn) addBtn.addEventListener('click', () => addSlot(widget))

        slotList.addEventListener('click', (event) => {
            const btn = event.target.closest(selectors.removeSlot)
            if (!btn) return
            const card = btn.closest(selectors.slotCard)
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
        const DELAY = 100

        if (typeof window !== 'undefined' && typeof window.Litepicker === 'undefined') {
            if (attempt >= MAX_ATTEMPTS) {
                console.warn('Litepicker nicht gefunden.')
                return
            }
            setTimeout(() => startOnceLibrariesAvailable(attempt + 1), DELAY)
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

document.addEventListener('DOMContent-loaded', () => BookingWidget.init())
document.addEventListener('DOMContentLoaded', () => BookingWidget.init())

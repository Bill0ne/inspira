jQuery(function ($) {
    const dateTimeFormat = 'DD.MM.YYYY HH:mm'

    const baseOptions = {
        format: dateTimeFormat,
        locale: 'de',
        stepping: 30,
        ignoreReadonly: true,
        useCurrent: false,
        icons: {
            time: 'far fa-clock',
            date: 'far fa-calendar-alt',
            up: 'fas fa-chevron-up',
            down: 'fas fa-chevron-down',
            previous: 'fas fa-chevron-left',
            next: 'fas fa-chevron-right',
            today: 'far fa-calendar-check',
            clear: 'fas fa-trash',
            close: 'fas fa-check',
        },
        buttons: {
            showToday: true,
            showClear: false,
            showClose: true,
        },
    }

    const parseValue = (value) => {
        if (!value) {
            return null
        }

        const parsed = moment(value, dateTimeFormat, true)

        return parsed.isValid() ? parsed : null
    }

    const getTarget = ($input) => {
        const $group = $input.closest('.input-group.date')

        return $group.length ? $group : $input
    }

    const initPicker = ($input, extraOptions = {}) => {
        const $target = getTarget($input)

        if (!$target.length) {
            return null
        }

        const currentValue = parseValue($input.val())

        const existing = $target.data('DateTimePicker')
        if (existing) {
            existing.destroy()
        }

        const options = $.extend(true, {}, baseOptions, extraOptions)

        if (typeof options.minDate === 'undefined') {
            options.minDate = moment()
        }

        if (options.minDate && currentValue && currentValue.isBefore(options.minDate)) {
            options.minDate = currentValue.clone()
        }

        $target.datetimepicker(options)

        const picker = $target.data('DateTimePicker')

        if (!picker) {
            return null
        }

        if (currentValue) {
            picker.date(currentValue)
        } else {
            picker.clear()
        }

        return picker
    }

    const initSlot = ($slot) => {
        const $startInput = $slot.find('.theme-date-input-start')
        const $endInput = $slot.find('.theme-date-input-end')

        if (!$startInput.length || !$endInput.length) {
            return
        }

        const startPicker = initPicker($startInput)
        const endPicker = initPicker($endInput, { useCurrent: false })

        const startTarget = getTarget($startInput)
        const endTarget = getTarget($endInput)

        const ensureEndAfterStart = (startMoment) => {
            if (!endPicker) {
                return
            }

            const effectiveStart = startMoment || moment()
            endPicker.minDate(effectiveStart)

            const endValue = parseValue($endInput.val())
            if (startMoment && (!endValue || endValue.isBefore(startMoment))) {
                endPicker.date(startMoment.clone().add(1, 'hour'))
            }
        }

        const ensureStartBeforeEnd = (endMoment) => {
            if (!startPicker) {
                return
            }

            if (endMoment) {
                startPicker.maxDate(endMoment.clone())
            } else {
                startPicker.maxDate(false)
            }
        }

        ensureEndAfterStart(parseValue($startInput.val()))
        ensureStartBeforeEnd(parseValue($endInput.val()))

        if (startTarget.length) {
            startTarget.on('change.datetimepicker', (event) => {
                const selected = event.date ? event.date.clone() : null
                ensureEndAfterStart(selected)
            })
        }

        if (endTarget.length) {
            endTarget.on('change.datetimepicker', (event) => {
                const selected = event.date ? event.date.clone() : null
                ensureStartBeforeEnd(selected)
            })
        }
    }

    $('.booking-slots-wrapper .slot-item').each(function () {
        initSlot($(this))
    })
})

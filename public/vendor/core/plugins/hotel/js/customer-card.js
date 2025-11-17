$(() => {
    const currency = (window.customerCard && window.customerCard.currency) || ''

    const t = (path, fallback = '') => {
        const segments = path.split('.')
        let current = window.trans && window.trans.customerCard

        for (const segment of segments) {
            if (! current || typeof current !== 'object' || !(segment in current)) {
                return fallback
            }

            current = current[segment]
        }

        return current ?? fallback
    }

    const formatPrice = (amount) => {
        const value = Number(amount || 0)

        return `${currency}${value.toFixed(2)}`
    }

    const $adminForm = $(document).find('form.customer-card-form')

    if ($adminForm.length) {
        const selectors = {
            basePrice: '[data-bb-customer-card-input="base-price"]',
            discount: '[data-bb-customer-card-input="discount"]',
            units: '[data-bb-customer-card-input="units-total"]',
            summaryText: '[data-bb-customer-card="summary-text"]',
            summaryTotal: '[data-bb-customer-card="summary-total"]',
            type: '[data-bb-customer-card-select="type"]',
        }

        const syncUnitsByType = (type) => {
            if (type === '5er' || type === '10er') {
                const units = type === '5er' ? 5 : 10
                $adminForm.find(selectors.units).val(units)
            }
        }

        const updateSummary = () => {
            const basePrice = parseFloat($adminForm.find(selectors.basePrice).val()) || 0
            const discount = parseFloat($adminForm.find(selectors.discount).val()) || 0
            const units = parseInt($adminForm.find(selectors.units).val(), 10) || 0

            if (! basePrice || ! units) {
                $adminForm.find(selectors.summaryText).text(t('form.summary.placeholder'))
                $adminForm.find(selectors.summaryTotal).text('')

                return
            }

            const gross = basePrice * units
            const discountAmount = gross * Math.min(Math.max(discount, 0), 100) / 100
            const net = gross - discountAmount

            $adminForm
                .find(selectors.summaryText)
                .text(`${formatPrice(gross)} → ${formatPrice(net)}`)

            $adminForm
                .find(selectors.summaryTotal)
                .text(`${discount.toFixed(2)}% ${t('form.summary.discount_label')}`)
        }

        $adminForm
            .on('change', selectors.type, (event) => {
                syncUnitsByType(event.currentTarget.value)
                updateSummary()
            })
            .on('keyup change', [
                selectors.basePrice,
                selectors.discount,
                selectors.units,
            ].join(','), updateSummary)

        syncUnitsByType($adminForm.find(selectors.type).val())
        updateSummary()
    }

    const $templateSelect = $('[data-bb-customer-card="template-select"]')

    if ($templateSelect.length) {
        const $summary = $('[data-bb-customer-card="template-summary"]')
        const $placeholder = $('[data-bb-customer-card="template-placeholder"]')
        const $name = $('[data-bb-customer-card="template-name"]')
        const $type = $('[data-bb-customer-card="template-type"]')
        const $basePrice = $('[data-bb-customer-card="template-base-price"]')
        const $totalPrice = $('[data-bb-customer-card="template-total-price"]')
        const $units = $('[data-bb-customer-card="template-units"]')
        const $discount = $('[data-bb-customer-card="template-discount"]')
        const $validUntil = $('[data-bb-customer-card="template-valid-until"]')
        const $validInput = $('[name="valid_until"]')

        const updateTemplateSummary = () => {
            const $option = $templateSelect.find(':selected')
            const templateId = Number($option.val()) || null

            if (! templateId) {
                $summary.hide()
                $placeholder.show()
                return
            }

            const basePrice = Number($option.data('base-price') || 0)
            const totalUnits = Number($option.data('total-units') || 0)
            const discountValue = Number($option.data('discount') || 0)
            const totalPrice = Number($option.data('total-price') || 0)
            const validUntil = $option.data('valid-until') || ''

            $name.text($option.data('name') || '')
            $type.text($option.data('type') || '')
            $basePrice.text(`${formatPrice(basePrice)} × ${totalUnits}`)
            $totalPrice.text(formatPrice(totalPrice))
            $units.text(totalUnits ? Number(totalUnits).toLocaleString() : '—')
            $discount.text(`${discountValue.toFixed(2)}%`)
            $validUntil.text(validUntil || '—')

            if (validUntil && $validInput.length) {
                $validInput.val(validUntil)
            }

            $placeholder.hide()
            $summary.show()
        }

        $templateSelect.on('change', updateTemplateSummary)
        updateTemplateSummary()
    }

    const csrfToken = () => $('meta[name="csrf-token"]').attr('content')

    const toggleButton = ($button, isLoading) => {
        if (!$button || !$button.length) {
            return
        }

        $button.prop('disabled', !!isLoading)
        $button.toggleClass('button-loading', !!isLoading)
    }

    const request = (url, payload = {}, $trigger = null) => {
        if (! url) {
            return Promise.reject(new Error('Missing URL'))
        }

        const data = {
            _token: csrfToken(),
            ...payload,
        }

        if (window.Botble?.request) {
            let client = window.Botble.request

            if ($trigger && typeof client.withButtonLoading === 'function') {
                client = client.withButtonLoading($trigger)
            }

            return client.post(url, data)
        }

        toggleButton($trigger, true)

        if (window.axios) {
            return window.axios.post(url, data).finally(() => toggleButton($trigger, false))
        }

        return $.ajax({
            method: 'POST',
            url,
            data,
            complete() {
                toggleButton($trigger, false)
            },
        }).then((response) => ({ data: response }))
    }

    const getRequest = (url, $trigger = null) => {
        if (! url) {
            return Promise.reject(new Error('Missing URL'))
        }

        if (window.Botble?.request) {
            let client = window.Botble.request

            if ($trigger && typeof client.withButtonLoading === 'function') {
                client = client.withButtonLoading($trigger)
            }

            return client.get(url)
        }

        toggleButton($trigger, true)

        if (window.axios) {
            return window.axios.get(url).finally(() => toggleButton($trigger, false))
        }

        return $.ajax({
            method: 'GET',
            url,
            complete() {
                toggleButton($trigger, false)
            },
        }).then((response) => ({ data: response }))
    }

    const resolveRoute = (key, fallback = null) => {
        if (window.customerCard && window.customerCard.routes && window.customerCard.routes[key]) {
            return window.customerCard.routes[key]
        }

        return fallback
    }

    const defaultApplyRoute = resolveRoute('apply')
    const defaultRemoveRoute = resolveRoute('remove')

    const getApplyRoute = () => resolveRoute('apply', defaultApplyRoute)
    const getRemoveRoute = () => resolveRoute('remove', defaultRemoveRoute)

    const applyCustomerCard = (cardId, courseId = null, $trigger = null) => request(getApplyRoute(), {
        card_id: cardId,
        course_id: courseId,
    }, $trigger)

    const removeCustomerCard = ($trigger = null) => request(getRemoveRoute(), {}, $trigger)

    window.customerCard = window.customerCard || {}
    window.customerCard.applyCustomerCard = applyCustomerCard
    window.customerCard.removeCustomerCard = removeCustomerCard

    const $cardSelect = $('#customer_card_select')

    if ($cardSelect.length) {
        const $applyButton = $('[data-bb-customer-card="apply"]')
        const $removeButton = $('[data-bb-customer-card="remove"]')
        const $infoBox = $('[data-bb-customer-card="info"]')
        const $totalInput = $('[data-total]')
        const $cardInput = $('[data-customer-card-input]')
        const $discountRow = $('.card-discount-row')
        const $discountText = $('.card-discount-text')
        const $minimumFeeRow = $('.minimum-fee-row')
        const $minimumFeeText = $('.minimum-fee-text')
        const $totalAmountText = $('.total-amount-text')
        const courseId = Number($cardSelect.data('course')) || null

        const getOriginalTotal = () => {
            const storedValue = $totalInput.data('original-total')

            if (typeof storedValue !== 'undefined' && storedValue !== null && storedValue !== '') {
                return Number(storedValue)
            }

            const currentTotal = Number($totalInput.val()) || 0
            const activeDiscount = Number($totalInput.data('active-discount') || 0)
            const minimumFee = Number($totalInput.data('minimum-fee') || 0)
            const base = currentTotal + activeDiscount - minimumFee

            $totalInput.data('original-total', base)

            return base
        }

        const updateTotals = (discountValue = 0, formattedDiscount = null) => {
            const baseTotal = getOriginalTotal()
            let nextTotal = Math.max(baseTotal - Number(discountValue || 0), 0)
            const threshold = Number($totalInput.data('minimum-threshold') || 0)
            let minimumFee = 0

            if (nextTotal > 0 && threshold > 0 && nextTotal < threshold) {
                minimumFee = parseFloat((threshold - nextTotal).toFixed(2))
                nextTotal = parseFloat((nextTotal + minimumFee).toFixed(2))
            } else {
                nextTotal = parseFloat(nextTotal.toFixed(2))
            }

            $totalInput
                .val(nextTotal.toFixed(2))
                .data('active-discount', Number(discountValue || 0))
                .data('minimum-fee', minimumFee)

            if ($discountRow.length) {
                if (discountValue > 0) {
                    $discountRow.removeClass('d-none')
                    if (formattedDiscount) {
                        $discountText.text(`-${formattedDiscount.replace(/^[-]/, '')}`)
                    } else {
                        $discountText.text(`-${formatPrice(discountValue)}`)
                    }
                } else {
                    $discountRow.addClass('d-none')
                    $discountText.text(`-${formatPrice(0)}`)
                }
            }

            if ($minimumFeeRow.length) {
                if (minimumFee > 0) {
                    $minimumFeeRow.removeClass('d-none')
                    if ($minimumFeeText.length) {
                        $minimumFeeText.text(formatPrice(minimumFee))
                    }
                } else {
                    $minimumFeeRow.addClass('d-none')
                    if ($minimumFeeText.length) {
                        $minimumFeeText.text(formatPrice(0))
                    }
                }
            }

            $totalAmountText.text(formatPrice(nextTotal))
        }

        if (Number($cardInput.val())) {
            $removeButton.removeClass('d-none')
        }

        updateTotals(Number($totalInput.data('active-discount') || 0))

        $applyButton.on('click', function (event) {
            event.preventDefault()

            const cardId = Number($cardSelect.val())

            if (! cardId) {
                Botble.showError(t('messages.select_card'))

                return
            }

            applyCustomerCard(cardId, courseId, $(this))
                .then(({ data }) => {
                    Botble.showSuccess(data.message)

                    const payload = data.data || {}

                    if (payload.discount) {
                        $infoBox
                            .removeClass('d-none')
                            .find('[data-bb-customer-card="discount"]').text(payload.discount)
                    }

                    updateTotals(Number(payload.raw_discount || 0), payload.discount)
                    $cardInput.val(cardId)
                    $removeButton.removeClass('d-none')

                    $(document).trigger('customer-card.applied', data)
                })
                .catch((error) => {
                    Botble.handleError(error)
                })
        })

        $removeButton.on('click', function (event) {
            event.preventDefault()

            removeCustomerCard($(this))
                .then(({ data }) => {
                    Botble.showSuccess(data.message)
                    $infoBox.addClass('d-none')
                    $cardSelect.val('')
                    $removeButton.addClass('d-none')
                    $cardInput.val('')
                    updateTotals(0)
                    $(document).trigger('customer-card.removed', data)
                })
                .catch((error) => {
                    Botble.handleError(error)
                })
        })

        $(document).on('customer-card.totals-updated', (event, payload = {}) => {
            const discountRaw = Number(payload.card_discount_raw || 0)
            const formattedDiscount = payload.card_discount_display_plain || null

            if (typeof payload.total_before_card_raw !== 'undefined') {
                $totalInput.data('original-total', Number(payload.total_before_card_raw))
            }

            if (typeof payload.minimum_fee_raw !== 'undefined') {
                $totalInput.data('minimum-fee', Number(payload.minimum_fee_raw || 0))
            }

            if (typeof payload.minimum_threshold !== 'undefined') {
                $totalInput.data('minimum-threshold', Number(payload.minimum_threshold) || 0)
            }

            if ($infoBox.length && Number($cardInput.val())) {
                $infoBox
                    .toggleClass('d-none', discountRaw <= 0)
                    .find('[data-bb-customer-card="discount"]')
                    .text(formattedDiscount || formatPrice(discountRaw))
            }

            if (discountRaw > 0) {
                $removeButton.removeClass('d-none')
            }

            updateTotals(discountRaw, formattedDiscount)
        })
    }

    const usageSelector = '[data-bb-customer-card="usage"]'

    const ensureUsageModal = () => {
        let $modal = $('#customer-card-usage-modal')

        if (! $modal.length) {
            $modal = $(
                `<div class="modal fade" id="customer-card-usage-modal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">${t('table.usage_title', 'Kartenverwendung')}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="text-center py-4">${t('messages.loading', 'Loading...')}</div>
                            </div>
                        </div>
                    </div>
                </div>`
            )

            $('body').append($modal)
        }

        return $modal
    }

    $(document).on('click', usageSelector, function () {
        const $trigger = $(this)
        const url = $trigger.data('url')
        const title = $trigger.data('title') || t('table.usage_title', 'Kartenverwendung')

        if (! url) {
            return
        }

        const $modal = ensureUsageModal()

        $modal.find('.modal-title').text(title)
        $modal
            .find('.modal-body')
            .html(`<div class="text-center py-4">${t('messages.loading', 'Loading...')}</div>`)

        $modal.modal('show')

        getRequest(url, $trigger)
            .then(({ data }) => {
                if (data.data && data.data.html) {
                    $modal.find('.modal-body').html(data.data.html)
                }
            })
            .catch((error) => {
                $modal.modal('hide')
                Botble.handleError(error)
            })
    })
})

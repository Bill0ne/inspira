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

    const request = (url, payload = {}, $trigger = null) => {
        if (! url) {
            return Promise.reject(new Error('Missing URL'))
        }

        return $httpClient.make()
            .withButtonLoading($trigger)
            .post(url, payload)
    }

    const applyRoute = window.customerCard && window.customerCard.routes && window.customerCard.routes.apply
    const removeRoute = window.customerCard && window.customerCard.routes && window.customerCard.routes.remove

    const applyCustomerCard = (cardId, courseId = null, $trigger = null) => request(applyRoute, {
        card_id: cardId,
        course_id: courseId,
    }, $trigger)

    const removeCustomerCard = ($trigger = null) => request(removeRoute, {}, $trigger)

    window.customerCard = window.customerCard || {}
    window.customerCard.applyCustomerCard = applyCustomerCard
    window.customerCard.removeCustomerCard = removeCustomerCard

    const $cardSelect = $('#customer_card_select')

    if ($cardSelect.length) {
        const $applyButton = $('[data-bb-customer-card="apply"]')
        const $removeButton = $('[data-bb-customer-card="remove"]')
        const $infoBox = $('[data-bb-customer-card="info"]')
        const courseId = Number($cardSelect.data('course')) || null

        $applyButton.on('click', function (event) {
            event.preventDefault()

            const cardId = Number($cardSelect.val())

            if (! cardId) {
                Botble.showError(t('messages.select_card'))

                return
            }

            applyCustomerCard(cardId, courseId, $(this)).then(({ data }) => {
                Botble.showSuccess(data.message)

                if (data.data && data.data.discount) {
                    $infoBox
                        .removeClass('d-none')
                        .find('[data-bb-customer-card="discount"]').text(data.data.discount)
                }

                $(document).trigger('customer-card.applied', data)
            }).catch((error) => {
                Botble.handleError(error)
            })
        })

        $removeButton.on('click', function (event) {
            event.preventDefault()

            removeCustomerCard($(this)).then(({ data }) => {
                Botble.showSuccess(data.message)
                $infoBox.addClass('d-none')
                $(document).trigger('customer-card.removed', data)
            }).catch((error) => {
                Botble.handleError(error)
            })
        })
    }
})

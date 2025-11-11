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
        const $totalInput = $('[data-total]')
        const $cardInput = $('[data-customer-card-input]')
        const $discountRow = $('.card-discount-row')
        const $discountText = $('.card-discount-text')
        const $totalAmountText = $('.total-amount-text')
        const courseId = Number($cardSelect.data('course')) || null

        const getOriginalTotal = () => {
            let base = Number($totalInput.data('original-total'))

            if (! base) {
                const currentTotal = Number($totalInput.val()) || 0
                const activeDiscount = Number($totalInput.data('active-discount') || 0)
                base = currentTotal + activeDiscount
                $totalInput.data('original-total', base)
            }

            return base
        }

        const updateTotals = (discountValue = 0, formattedDiscount = null) => {
            const baseTotal = getOriginalTotal()
            const nextTotal = Math.max(baseTotal - Number(discountValue || 0), 0)

            $totalInput
                .val(nextTotal.toFixed(2))
                .data('active-discount', Number(discountValue || 0))

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
    }

    const $usageButtons = $('[data-bb-customer-card="usage"]')

    if ($usageButtons.length) {
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

        $usageButtons.on('click', function () {
            const url = $(this).data('url')
            const title = $(this).data('title') || t('table.usage_title', 'Kartenverwendung')

            if (! url) {
                return
            }

            const $modal = ensureUsageModal()

            $modal.find('.modal-title').text(title)
            $modal
                .find('.modal-body')
                .html(`<div class="text-center py-4">${t('messages.loading', 'Loading...')}</div>`)

            $modal.modal('show')

            $httpClient.make()
                .get(url)
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
    }
})

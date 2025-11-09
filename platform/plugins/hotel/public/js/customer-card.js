$(() => {
    const $form = $(document).find('form.customer-card-form')

    if (! $form.length) {
        return
    }

    const currency = (window.customerCard && window.customerCard.currency) || ''

    const getTranslation = (path, fallback = '') => {
        const segments = path.split('.')
        let current = window.trans && window.trans.customerCard

        for (const segment of segments) {
            if (! current || typeof current !== 'object' || !(segment in current)) {
                return fallback
            }

            current = current[segment]
        }

        if (current === undefined || current === null) {
            return fallback
        }

        return current
    }

    const selectors = {
        basePrice: '[data-bb-customer-card-input="base-price"]',
        discount: '[data-bb-customer-card-input="discount"]',
        unitsTotal: '[data-bb-customer-card-input="units-total"]',
        unitsRemaining: '[data-bb-customer-card-input="units-remaining"]',
        summary: '[data-bb-customer-card="summary"]',
        summaryText: '[data-bb-customer-card="summary-text"]',
        type: '[data-bb-customer-card-select="type"]',
    }

    const updateSummary = () => {
        const basePrice = parseFloat($form.find(selectors.basePrice).val()) || 0
        const discount = parseFloat($form.find(selectors.discount).val()) || 0
        const units = parseInt($form.find(selectors.unitsTotal).val()) || 0

        if (! basePrice || ! units) {
            $form.find(selectors.summaryText).text(
                getTranslation('form.summary.placeholder', '')
            )

            return
        }

        const gross = basePrice * units
        const discountFactor = Math.max(Math.min(discount, 100), 0) / 100
        const net = gross * (1 - discountFactor)

        $form
            .find(selectors.summaryText)
            .text(
                `${currency}${gross.toFixed(2)} → ${currency}${net.toFixed(2)} (${discount.toFixed(0)}% ${getTranslation('form.summary.discount_label', '')})`
            )
    }

    const syncUnitsByType = (type) => {
        const $unitsTotal = $form.find(selectors.unitsTotal)
        const $unitsRemaining = $form.find(selectors.unitsRemaining)

        if (type === '5er') {
            $unitsTotal.val(5)
        }

        if (type === '10er') {
            $unitsTotal.val(10)
        }

        if (type !== 'custom') {
            $unitsRemaining.val($unitsTotal.val())
        }

        updateSummary()
    }

    $form
        .on('change keyup', [
            selectors.basePrice,
            selectors.discount,
            selectors.unitsTotal,
        ].join(','), () => {
            updateSummary()
        })
        .on('change', selectors.type, (e) => {
            syncUnitsByType(e.currentTarget.value)
        })

    syncUnitsByType($form.find(selectors.type).val())
    updateSummary()
})

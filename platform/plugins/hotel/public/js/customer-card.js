$(() => {

    const showSuccess = (message) => {
        if (window.Botble?.showSuccess) {
            window.Botble.showSuccess(message);
            return;
        }

        console.log('Success:', message);
    };

    const showError = (message) => {
        if (window.Botble?.showError) {
            window.Botble.showError(message);
            return;
        }

        console.error('Error:', message);
    };

    const handleError = (error) => {
        if (window.Botble?.handleError) {
            window.Botble.handleError(error);
            return;
        }

        console.error('Request error:', error);
    };

    const extractErrorMessage = (error) => {
        if (!error) return null;

        if (typeof error === 'string') return error;

        if (error.responseJSON?.message) return error.responseJSON.message;

        if (error.responseJSON?.data?.message) return error.responseJSON.data.message;

        if (error.responseJSON?.error) return error.responseJSON.error;

        if (error.responseText) return error.responseText;

        if (error.message) return error.message;

        return null;
    };

    /* ----------------------------------------------------------
     *  CONFIG
     * ---------------------------------------------------------- */
    const CARD_CONFIG = window.customerCard || {};
    window.customerCard = CARD_CONFIG;

    CARD_CONFIG.routes = CARD_CONFIG.routes || {};
    CARD_CONFIG.course_checkout = CARD_CONFIG.course_checkout || false;
    CARD_CONFIG.isAuthenticated = CARD_CONFIG.isAuthenticated || false;

    const t = (path, fallback = '') => {
        const segments = path.split('.');
        let current = window.trans && window.trans.customerCard;

        for (const segment of segments) {
            if (!current || typeof current !== 'object' || !(segment in current)) {
                return fallback;
            }
            current = current[segment];
        }
        return current ?? fallback;
    };

    const formatPrice = (amount) => {
        const value = Number(amount || 0);
        return `${CARD_CONFIG.currency || ''}${value.toFixed(2)}`;
    };

    const csrfToken = () => $('meta[name="csrf-token"]').attr('content');

    const toggleButton = ($button, isLoading) => {
        if (!$button || !$button.length) return;
        $button.prop('disabled', !!isLoading).toggleClass('button-loading', !!isLoading);
    };

    const handleRequestError = (error) => {
        const message = extractErrorMessage(error);

        if (message) {
            showError(message);
            return;
        }

        handleError(error);
    };

    const isCustomerAuthenticated = () => !!CARD_CONFIG.isAuthenticated;

    const ensureRouteAvailable = (routeKey = null) => {
        if (!routeKey) {
            return true;
        }

        if (CARD_CONFIG.routes && CARD_CONFIG.routes[routeKey]) {
            return true;
        }

        showError(t('messages.route_unavailable', 'Der Kundenkarten-Service ist aktuell nicht verfügbar.'));
        return false;
    };

    const ensureCustomerIsAuthenticated = () => {
        if (isCustomerAuthenticated()) {
            return true;
        }

        showError(t('messages.login_required', 'Bitte zuerst einloggen.'));
        return false;
    };

    const showCheckoutError = (message) => {
        const $inlineError = $('[data-bb-customer-card="error"], .customer-card-error');

        if ($inlineError.length) {
            $inlineError
                .removeClass('d-none')
                .text(message || t('messages.checkout_unavailable', 'Die Checkout-Antwort ist unvollständig.'));
            return;
        }

        showError(message || t('messages.checkout_unavailable', 'Die Checkout-Antwort ist unvollständig.'));
    };

    const validateCheckoutResponse = (payload, requireCourse = false) => {
        if (!payload || typeof payload !== 'object') return false;

        const totals = payload.totals;
        if (!totals || typeof totals !== 'object') return false;

        const requiredTotals = ['total_raw', 'minimum_fee_raw', 'card_discount_raw'];
        const hasTotals = requiredTotals.every((key) => Object.prototype.hasOwnProperty.call(totals, key));
        const hasCourse = !requireCourse || (payload.course && Number(payload.course.id));

        return hasTotals && hasCourse;
    };

    const resetControls = ($button = null, ...controls) => {
        toggleButton($button, false);
        controls.filter(Boolean).forEach(($control) => $control.prop('disabled', false).removeClass('button-loading'));
    };

    /* ----------------------------------------------------------
     *  UNIVERSAL REQUEST WRAPPER
     * ---------------------------------------------------------- */
    const createRequestClient = ($trigger = null) => {
        let client = window.Botble?.request;

        if (client && $trigger && typeof client.withButtonLoading === 'function') {
            client = client.withButtonLoading($trigger);
        }

        if (client && (typeof client.post === 'function' || typeof client.get === 'function')) {
            return client;
        }

        const ajaxRequest = (method, url, data = {}) =>
            $.ajax({
                url,
                type: method,
                data,
                dataType: 'json',
                headers: {
                    Accept: 'application/json',
                    ...(method === 'POST' ? { 'X-CSRF-TOKEN': csrfToken() } : {}),
                },
                beforeSend: () => {
                    if ($trigger) {
                        $trigger.addClass('button-loading').attr('disabled', true);
                    }
                },
                complete: () => {
                    if ($trigger) {
                        $trigger.removeClass('button-loading').attr('disabled', false);
                    }
                },
            }).then((response) => ({ data: response }));

        return {
            post: (url, data = {}) => ajaxRequest('POST', url, { _token: csrfToken(), ...data }),
            get: (url, data = {}) => ajaxRequest('GET', url, data),
        };
    };

    const request = (url, payload = {}, $trigger = null) => {
        if (!url) return Promise.reject(new Error('Missing URL'));

        const client = createRequestClient($trigger);

        if (typeof client.post === 'function') {
            return client.post(url, payload);
        }

        return Promise.reject(new Error('No request client available'));
    };

    const triggerCustomerCardEvent = (eventName, detail = {}) => {
        $(document).trigger(eventName, detail);

        if (window.document && typeof window.CustomEvent === 'function') {
            window.document.dispatchEvent(new CustomEvent(eventName, { detail }));
        }

        if (
            eventName === 'customer-card.removed' &&
            window.RioRelaxCourseCheckout &&
            typeof window.RioRelaxCourseCheckout.refreshCourseCoupon === 'function'
        ) {
            window.RioRelaxCourseCheckout.refreshCourseCoupon();
        }
    };

    const resolveCoursePayload = (courseId = null) => {
        const payload = {};
        const configuredId = Number(courseId || CARD_CONFIG.courseId || CARD_CONFIG.course_id || 0);

        if (configuredId) {
            payload.course_id = configuredId;
        }

        if (payload.course_id || CARD_CONFIG.course_checkout) {
            payload.course_checkout = true;
        }

        return payload;
    };

    const applyCustomerCard = (cardId, courseId = null, $trigger = null) =>
        request(
            CARD_CONFIG.routes.apply,
            { card_id: cardId, ...resolveCoursePayload(courseId) },
            $trigger,
        );

    const removeCustomerCard = ($trigger = null, courseId = null) =>
        request(CARD_CONFIG.routes.remove, resolveCoursePayload(courseId), $trigger);

    CARD_CONFIG.applyCustomerCard = applyCustomerCard;
    CARD_CONFIG.removeCustomerCard = removeCustomerCard;

    const syncCheckoutState = (payload, { trigger = null } = {}) => {
        if (!validateCheckoutResponse(payload, CARD_CONFIG.course_checkout)) {
            showCheckoutError(t('messages.checkout_unavailable', 'Die Checkout-Antwort ist unvollständig.'));
            resetControls(trigger);
            return payload;
        }

        if (window.CheckoutCommerce && typeof window.CheckoutCommerce.setState === 'function') {
            return window.CheckoutCommerce.setState(payload, 'course');
        }

        if (typeof window.renderCheckoutUI === 'function') {
            window.CheckoutState = payload || {};
            return window.renderCheckoutUI('course');
        }

        return payload;
    };

    /* ----------------------------------------------------------
     *  ADMIN FORM SYNC
     * ---------------------------------------------------------- */
    const $adminForm = $('form.customer-card-form');
    if ($adminForm.length) {
        const selectors = {
            basePrice: '[data-bb-customer-card-input="base-price"]',
            discount: '[data-bb-customer-card-input="discount"]',
            units: '[data-bb-customer-card-input="units-total"]',
            summaryText: '[data-bb-customer-card="summary-text"]',
            summaryTotal: '[data-bb-customer-card="summary-total"]',
            type: '[data-bb-customer-card-select="type"]',
        };

        const syncUnitsByType = (type) => {
            if (type === '5er' || type === '10er') {
                $adminForm.find(selectors.units).val(type === '5er' ? 5 : 10);
            }
        };

        const updateSummary = () => {
            const basePrice = parseFloat($adminForm.find(selectors.basePrice).val()) || 0;
            const discount = parseFloat($adminForm.find(selectors.discount).val()) || 0;
            const units = parseInt($adminForm.find(selectors.units).val(), 10) || 0;

            if (!basePrice || !units) {
                $adminForm.find(selectors.summaryText).text(t('form.summary.placeholder'));
                $adminForm.find(selectors.summaryTotal).text('');
                return;
            }

            const gross = basePrice * units;
            const discountAmount = gross * Math.min(Math.max(discount, 0), 100) / 100;
            const net = gross - discountAmount;

            $adminForm.find(selectors.summaryText).text(`${formatPrice(gross)} → ${formatPrice(net)}`);
            $adminForm.find(selectors.summaryTotal).text(`${discount.toFixed(2)}% ${t('form.summary.discount_label')}`);
        };

        $adminForm
            .on('change', selectors.type, (e) => {
                syncUnitsByType(e.currentTarget.value);
                updateSummary();
            })
            .on('keyup change', [
                selectors.basePrice,
                selectors.discount,
                selectors.units,
            ].join(','), updateSummary);

        syncUnitsByType($adminForm.find(selectors.type).val());
        updateSummary();
    }

    /* ----------------------------------------------------------
     *  FRONTEND CHECKOUT
     * ---------------------------------------------------------- */
    const $cardSelect = $('#customer_card_select');

    if ($cardSelect.length) {
        const $applyButton = $('[data-bb-customer-card="apply"]');
        const $removeButton = $('[data-bb-customer-card="remove"]');
        const $infoBox = $('[data-bb-customer-card="info"]');
        const $totalInput = $('[data-total]');
        const $cardInput = $('[data-customer-card-input]');
        const $discountRow = $('.card-discount-row');
        const $discountText = $('.card-discount-text');
        const $minimumFeeRow = $('.minimum-fee-row');
        const $minimumFeeText = $('.minimum-fee-text');
        const $totalAmountText = $('.total-amount-text');

        const fallbackCourseId = Number(CARD_CONFIG.courseId || CARD_CONFIG.course_id || 0) || null;
        const courseId = Number($cardSelect.data('course')) || fallbackCourseId;

        if (courseId && !CARD_CONFIG.courseId) {
            CARD_CONFIG.courseId = courseId;
        }

        if (courseId) {
            CARD_CONFIG.course_checkout = true;
        }

        const getOriginalTotal = () => {
            const stored = $totalInput.data('original-total');
            if (stored !== undefined && stored !== null && stored !== '') return Number(stored);

            const current = Number($totalInput.val()) || 0;
            const activeDiscount = Number($totalInput.data('active-discount') || 0);
            const minimumFee = Number($totalInput.data('minimum-fee') || 0);

            const base = current + activeDiscount - minimumFee;
            $totalInput.data('original-total', base);
            return base;
        };

        const updateTotals = (discountValue = 0, formattedDiscount = null) => {
            const baseTotal = getOriginalTotal();
            let nextTotal = Math.max(baseTotal - Number(discountValue || 0), 0);

            const threshold = Number($totalInput.data('minimum-threshold') || 0);
            let minimumFee = 0;

            if (nextTotal > 0 && threshold > 0 && nextTotal < threshold) {
                minimumFee = parseFloat((threshold - nextTotal).toFixed(2));
                nextTotal = parseFloat((nextTotal + minimumFee).toFixed(2));
            } else {
                nextTotal = parseFloat(nextTotal.toFixed(2));
            }

            $totalInput
                .val(nextTotal.toFixed(2))
                .data('active-discount', Number(discountValue || 0))
                .data('minimum-fee', minimumFee);

            if (discountValue > 0) {
                $discountRow.removeClass('d-none');
                $discountText.text(`-${formattedDiscount ? formattedDiscount.replace(/^[-]/, '') : formatPrice(discountValue)}`);
            } else {
                $discountRow.addClass('d-none');
                $discountText.text(`-${formatPrice(0)}`);
            }

            if (minimumFee > 0) {
                $minimumFeeRow.removeClass('d-none');
                $minimumFeeText.text(formatPrice(minimumFee));
            } else {
                $minimumFeeRow.addClass('d-none');
                $minimumFeeText.text(formatPrice(0));
            }

            $totalAmountText.text(formatPrice(nextTotal));
        };

        // Vorinitialisierung
        if (Number($cardInput.val())) $removeButton.removeClass('d-none');
        updateTotals(Number($totalInput.data('active-discount') || 0));

        /* APPLY -------------------------------------------------- */
        $applyButton.on('click', function (e) {
            e.preventDefault();

            const cardId = Number($cardSelect.val());
            if (!cardId) {
                showError(t('messages.select_card'));
                return;
            }

            if (!ensureCustomerIsAuthenticated() || !ensureRouteAvailable('apply')) {
                return;
            }

            applyCustomerCard(cardId, courseId, $(this))
                .then(({ data }) => {
                    const payload = data?.data || {};
                    if (!validateCheckoutResponse(payload, CARD_CONFIG.course_checkout)) {
                        showCheckoutError(t('messages.checkout_unavailable', 'Die Checkout-Antwort ist unvollständig.'));
                        resetControls($applyButton, $cardSelect);
                        return;
                    }

                    syncCheckoutState(payload, { trigger: $applyButton });
                    showSuccess(data.message);

                    triggerCustomerCardEvent('customer-card.applied', {
                        discount: Number(payload?.totals?.card_discount_raw || payload.raw_discount || 0),
                    });
                })
                .catch((error) => {
                    resetControls($applyButton, $cardSelect);
                    handleRequestError(error);
                });
        });

        /* REMOVE ------------------------------------------------- */
        $removeButton.on('click', function (e) {
            e.preventDefault();

            if (!ensureRouteAvailable('remove')) {
                return;
            }

            removeCustomerCard($(this), courseId)
                .then(({ data }) => {
                    const payload = data?.data || {};
                    if (!validateCheckoutResponse(payload, CARD_CONFIG.course_checkout)) {
                        showCheckoutError(t('messages.checkout_unavailable', 'Die Checkout-Antwort ist unvollständig.'));
                        resetControls($removeButton, $cardSelect);
                        return;
                    }

                    syncCheckoutState(payload, { trigger: $removeButton });
                    showSuccess(data.message);

                    triggerCustomerCardEvent('customer-card.removed', {});
                })
                .catch((error) => {
                    resetControls($removeButton, $cardSelect);
                    handleRequestError(error);
                });
        });
    }

});

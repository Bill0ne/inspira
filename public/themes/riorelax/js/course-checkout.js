$(document).ready(function () {

    let isRefreshingCoupon = false;
    let pendingCouponRefresh = false;

    // Zentrale Funktion: holt alle Beträge vom Backend (/course/ajax/calculate-amount)
    const refreshCourseCoupon = () => {
        if (isRefreshingCoupon) {
            pendingCouponRefresh = true;
            return;
        }

        isRefreshingCoupon = true;

        const $checkoutButton = $('.payment-checkout-btn');
        const disableCheckout = () => $checkoutButton.prop('disabled', true);
        const finishRefresh = () => {
            $checkoutButton.prop('disabled', false);
            isRefreshingCoupon = false;

            if (pendingCouponRefresh) {
                pendingCouponRefresh = false;
                refreshCourseCoupon();
            }
        };

        disableCheckout();

        const $selectedPaymentMethod = $('.payment-checkout-form .list_payment_method input[name="payment_method"]:checked').val();

        // Coupon-Code (Hidden > Eingabefeld)
        const couponCode =
            $('input[name=coupon_hidden]').val() ||
            $('input[name=coupon_code]').val() ||
            '';

        $.ajax({
            url: '/course/ajax/calculate-amount',
            type: 'GET',
            data: {
                course_id: $('input[name=course_id]').val(),
                coupon_code: couponCode,
            },
        })
            .done(({ error, message, data }) => {
                if (error) {
                    if (window.RiorelaxTheme) {
                        window.RiorelaxTheme.showError(message);
                    }
                    finishRefresh();
                    return;
                }

                const $totalInput = $('input[name=amount]');
                const $cardDiscountRow = $('.card-discount-row');
                const $cardDiscountText = $('.card-discount-text');
                const $minimumFeeRow = $('.minimum-fee-row');
                const $minimumFeeText = $('.minimum-fee-text');
                const $cardInfoBox = $('[data-bb-customer-card="info"]');
                const $cardInfoDiscount = $cardInfoBox.find('[data-bb-customer-card="discount"]');
                const cardDiscountRaw = Number(data.card_discount_raw || 0);
                const minimumFeeRaw = Number(data.minimum_fee_raw || 0);

                // Hidden Inputs & Data-Attribute aktualisieren
                $totalInput
                    .val(data.amount_raw)
                    .data('original-total', Number(data.total_before_card_raw || data.amount_raw))
                    .data('active-discount', cardDiscountRaw)
                    .data('minimum-fee', minimumFeeRaw)
                    .data('minimum-threshold', Number(data.minimum_threshold || 0));

                // Sidebar-Werte
                $('.total-amount-text').text(data.total_amount);
                $('.amount-text').text(data.sub_total);
                $('.discount-text').text(data.discount_amount);
                $('.tax-text').text(data.tax_amount);

                if ($cardDiscountRow.length) {
                    if (cardDiscountRaw > 0) {
                        $cardDiscountRow.removeClass('d-none');
                    } else {
                        $cardDiscountRow.addClass('d-none');
                    }

                    if (data.card_discount_display) {
                        $cardDiscountText.text(data.card_discount_display);
                    }
                }

                if ($minimumFeeRow.length) {
                    if (minimumFeeRaw > 0) {
                        $minimumFeeRow.removeClass('d-none');
                    } else {
                        $minimumFeeRow.addClass('d-none');
                    }

                    if (data.minimum_fee_display) {
                        $minimumFeeText.text(data.minimum_fee_display);
                    }
                }

                if ($cardInfoBox.length && Number($('[data-customer-card-input]').val())) {
                    if (cardDiscountRaw > 0) {
                        $cardInfoBox.removeClass('d-none');
                    } else {
                        $cardInfoBox.addClass('d-none');
                    }

                    if (data.card_discount_display_plain) {
                        $cardInfoDiscount.text(data.card_discount_display_plain);
                    }
                }

                $(document).trigger('customer-card.totals-updated', data);

                // Payment Methods neu laden (Auswahl beibehalten)
                const $paymentMethods = $('.payment-checkout-form .list_payment_method');
                if ($paymentMethods.length) {
                    $paymentMethods.load(
                        window.location.href + ' .payment-checkout-form .list_payment_method > *',
                        function (responseText, status, xhr) {
                            if (status === 'error' && window.RiorelaxTheme) {
                                window.RiorelaxTheme.handleError(xhr);
                            }

                            $paymentMethods
                                .find('input[value="' + $selectedPaymentMethod + '"]')
                                .prop('checked', true)
                                .trigger('change');

                            finishRefresh();
                        }
                    );
                } else {
                    finishRefresh();
                }

                // Optional: Detail-Box (z.B. Gutscheinzeilen) neu laden
                const $orderBox = $('.order-detail-box');
                const refreshUrl = $orderBox.data('refresh-url');
                if (refreshUrl) {
                    $.ajax({
                        url: refreshUrl,
                        type: 'GET',
                        data: { coupon_code: couponCode },
                    })
                        .done(({ error: refreshError, message: refreshMessage, data: refreshData }) => {
                            if (!refreshError && refreshData) {
                                $orderBox.html(refreshData);
                                if (
                                    window.CheckoutCommerce &&
                                    typeof window.CheckoutCommerce.restoreCouponFormState === 'function'
                                ) {
                                    window.CheckoutCommerce.restoreCouponFormState('course');
                                }
                            } else if (refreshError && window.RiorelaxTheme) {
                                window.RiorelaxTheme.showError(refreshMessage);
                            }
                        })
                        .fail((err) => window.RiorelaxTheme && window.RiorelaxTheme.handleError(err));
                }
            })
            .fail((err) => {
                if (window.RiorelaxTheme) {
                    window.RiorelaxTheme.handleError(err);
                }
                finishRefresh();
            });
    };

    window.RioRelaxCourseCheckout = window.RioRelaxCourseCheckout || {};
    window.RioRelaxCourseCheckout.refreshCourseCoupon = refreshCourseCoupon;

    // ⚠️ WICHTIG:
    // KEINE eigenen Event-Handler mehr für:
    //  - .toggle-coupon-form
    //  - .apply-coupon-code
    //  - .remove-coupon-code
    // Das übernimmt checkout-commerce.js exklusiv.

    // Reaktion auf Kundenkarten-Ereignisse:
    $(document).on('customer-card.applied customer-card.removed', refreshCourseCoupon);

    // Reaktion auf Coupon-Ereignisse aus checkout-commerce.js:
    $(document).on('coupon.applied coupon.removed', function () {
        refreshCourseCoupon();
    });
});

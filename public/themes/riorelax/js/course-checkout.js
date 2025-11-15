
$(document).ready(function () {

    let isRefreshingCoupon = false;
    let pendingCouponRefresh = false;

    // Function to refresh booking amounts and coupon box
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

        // Get coupon code from hidden input or user input
        const couponCode = $('input[name=coupon_hidden]').val() || $('input[name=coupon_code]').val() || '';

        // Calculate booking amount via AJAX
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
                    RiorelaxTheme.showError(message);
                    finishRefresh();
                    return;
                }

                // Update sidebar totals
                const $totalInput = $('input[name=amount]');
                const $cardDiscountRow = $('.card-discount-row');
                const $cardDiscountText = $('.card-discount-text');
                const $minimumFeeRow = $('.minimum-fee-row');
                const $minimumFeeText = $('.minimum-fee-text');
                const $cardInfoBox = $('[data-bb-customer-card="info"]');
                const $cardInfoDiscount = $cardInfoBox.find('[data-bb-customer-card="discount"]');
                const cardDiscountRaw = Number(data.card_discount_raw || 0);
                const minimumFeeRaw = Number(data.minimum_fee_raw || 0);

                $totalInput
                    .val(data.amount_raw)
                    .data('original-total', Number(data.total_before_card_raw || data.amount_raw))
                    .data('active-discount', cardDiscountRaw)
                    .data('minimum-fee', minimumFeeRaw)
                    .data('minimum-threshold', Number(data.minimum_threshold || 0));

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

                // Reload payment methods (preserve selection)
                const $paymentMethods = $('.payment-checkout-form .list_payment_method');
                if ($paymentMethods.length) {
                    $paymentMethods.load(
                        window.location.href + ' .payment-checkout-form .list_payment_method > *',
                        function (responseText, status, xhr) {
                            if (status === 'error') {
                                RiorelaxTheme.handleError(xhr);
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

                // Refresh order detail box (coupon info)
                const refreshUrl = $('.order-detail-box').data('refresh-url');
                if (refreshUrl) {
                    $.ajax({
                        url: refreshUrl,
                        type: 'GET',
                        data: { coupon_code: couponCode },
                    })
                        .done(({ error: refreshError, message: refreshMessage, data: refreshData }) => {
                            if (!refreshError) {
                                $('.order-detail-box').html(refreshData);
                            } else {
                                RiorelaxTheme.showError(refreshMessage);
                            }
                        })
                        .fail((err) => RiorelaxTheme.handleError(err));
                }
            })
            .fail((err) => {
                RiorelaxTheme.handleError(err);
                finishRefresh();
            });
    };

    window.RioRelaxCourseCheckout = window.RioRelaxCourseCheckout || {};
    window.RioRelaxCourseCheckout.refreshCourseCoupon = refreshCourseCoupon;

    // Toggle coupon form
    $(document)
        .on('click', '.toggle-coupon-form', () => $('.coupon-form').toggle('fast'))

        // Apply coupon
        .on('click', '.apply-coupon-code', (e) => {
            e.preventDefault();
            const $button = $(e.currentTarget);
            const couponCode = $('input[name=coupon_code]').val();

            if (!couponCode) {
                RiorelaxTheme.showError('Please enter a coupon code.');
                return;
            }

            $.ajax({
                url: $button.data('url'),
                type: 'POST',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: { coupon_code: couponCode },
                beforeSend: () => $button.addClass('button-loading'),
                success: ({ error, message }) => {
                    if (error) {
                        RiorelaxTheme.showError(message);
                        return;
                    }
                    RiorelaxTheme.showSuccess(message);
                    refreshCourseCoupon();
                },
                error: (err) => RiorelaxTheme.handleError(err),
                complete: () => $button.removeClass('button-loading'),
            });
        })

        // Remove coupon
        .on('click', '.remove-coupon-code', (e) => {
            e.preventDefault();
            const $button = $(e.currentTarget);

            $.ajax({
                url: $button.data('url'),
                type: 'POST',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                beforeSend: () => $button.addClass('button-loading'),
                success: ({ error, message }) => {
                    if (error) {
                        RiorelaxTheme.showError(message);
                        return;
                    }
                    RiorelaxTheme.showSuccess(message);
                    refreshCourseCoupon();
                },
                error: (err) => RiorelaxTheme.handleError(err),
                complete: () => $button.removeClass('button-loading'),
            });
        });

    $(document).on('customer-card.applied customer-card.removed', refreshCourseCoupon);
});

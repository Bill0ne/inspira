'use strict';

$(document).ready(function () {

    // Function to refresh booking amounts and coupon box
    const refreshCourseCoupon = () => {
        const $checkoutButton = $('.payment-checkout-btn');
        $checkoutButton.prop('disabled', true);

        const $selectedPaymentMethod = $('.payment-checkout-form .list_payment_method input[name="payment_method"]:checked').val();

        // Get coupon code from hidden input or user input
        const couponCode = $('input[name=coupon_hidden]').val() || $('input[name=coupon_code]').val();

        // Calculate booking amount via AJAX
        $.ajax({
            url: '/course/ajax/calculate-amount',
            type: 'GET',
            data: {
                course_id: $('input[name=course_id]').val(),
                coupon_code: couponCode,
            },
            success: ({ error, message, data }) => {
                if (error) {
                    RiorelaxTheme.showError(message);
                    return;
                }

                // Update sidebar totals
                $('.total-amount-text').text(data.total_amount);
                $('input[name=amount]').val(data.amount_raw);
                $('.amount-text').text(data.sub_total);
                $('.discount-text').text(data.discount_amount);
                $('.tax-text').text(data.tax_amount);

                // Reload payment methods (preserve selection)
                $('.payment-checkout-form .list_payment_method').load(
                    window.location.href + ' .payment-checkout-form .list_payment_method > *',
                    function () {
                        $checkoutButton.prop('disabled', false);
                        $('.payment-checkout-form .list_payment_method input[value="' + $selectedPaymentMethod + '"]')
                            .prop('checked', true)
                            .trigger('change');
                    }
                );

                // Refresh order detail box (coupon info)
                const refreshUrl = $('.order-detail-box').data('refresh-url');
                $.ajax({
                    url: refreshUrl,
                    type: 'GET',
                    data: { coupon_code: couponCode },
                    success: ({ error, message, data }) => {
                        if (!error) {
                            $('.order-detail-box').html(data);
                        } else {
                            RiorelaxTheme.showError(message);
                        }
                    },
                    error: (err) => RiorelaxTheme.handleError(err),
                });
            },
            error: (err) => RiorelaxTheme.handleError(err),
        });
    };

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
});

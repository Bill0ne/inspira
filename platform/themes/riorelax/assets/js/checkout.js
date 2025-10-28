'use strict'

$(document).ready(function () {
    $('.service-item').on('change', function () {
        const foods = []
        const services = []
        $('.service-item:checked').each((i, el) => {
            services[i] = $(el).val()
        })

        const slots = []
        $('input[name^="slots["][name$="[start_date]"]').each(function (i, el) {
            const start = $(el).val()
            const end = $(document).find(`input[name="slots[${i}][end_date]"]`).val()
            if (start && end) {
                slots.push({ start_date: start, end_date: end })
            }
        })

        $('.food-item:checked').each((i, el) => {
            foods[i] = $(el).val()
        })

        $('body').css('cursor', 'progress')
        $('.custom-checkbox label').css('cursor', 'progress')

        let $checkoutButton = $(document).find('.payment-checkout-btn')
        $checkoutButton.prop('disabled', true)
        let $selectedPaymentMethod = $(document).find('.payment-checkout-form .list_payment_method input[name="payment_method"]:checked').val()

        $.ajax({
            type: 'GET',
            cache: false,
            url: '/ajax/calculate-amount',
            data: {
                room_id: $('input[name=room_id]').val(),
                slots: slots,
                services,
                foods
            },
            success: ({ error, data }) => {
                if (!error) {
                    $('.total-amount-text').text(data.total_amount)
                    $('input[name=amount]').val(data.amount_raw)
                    $('.amount-text').text(data.sub_total)
                    $('.discount-text').text(data.discount_amount)
                    $('.tax-text').text(data.tax_amount)
                }

                $('body').css('cursor', 'default')
                $('.custom-checkbox label').css('cursor', 'pointer')

                $('.payment-checkout-form .list_payment_method').load(window.location.href + ' .payment-checkout-form .list_payment_method > *', function() {
                    $checkoutButton.prop('disabled', false)
                    $(document).find('.payment-checkout-form .list_payment_method input[value="' + $selectedPaymentMethod + '"]').prop('checked', true).trigger('change')
                })
            },
            error: () => {
                $('body').css('cursor', 'default')
                $('.custom-checkbox label').css('cursor', 'pointer')
                $checkoutButton.prop('disabled', false)
            },
        })
    })

    $('.food-item').on('change', function () {
        const foods = []
        const services = []
        $('.food-item:checked').each((i, el) => {
            foods[i] = $(el).val()
        })

        const slots = []
        $('input[name^="slots["][name$="[start_date]"]').each(function (i, el) {
            const start = $(el).val()
            const end = $(document).find(`input[name="slots[${i}][end_date]"]`).val()
            if (start && end) {
                slots.push({ start_date: start, end_date: end })
            }
        })

        $('.service-item:checked').each((i, el) => {
            services[i] = $(el).val()
        })

        $('body').css('cursor', 'progress')
        $('.custom-checkbox label').css('cursor', 'progress')

        let $checkoutButton = $(document).find('.payment-checkout-btn')
        $checkoutButton.prop('disabled', true)
        let $selectedPaymentMethod = $(document).find('.payment-checkout-form .list_payment_method input[name="payment_method"]:checked').val()

        $.ajax({
            type: 'GET',
            cache: false,
            url: '/ajax/calculate-amount',
            data: {
                room_id: $('input[name=room_id]').val(),
                slots: slots,
                foods,
                services,
            },
            success: ({ error, data }) => {
                if (!error) {
                    $('.total-amount-text').text(data.total_amount)
                    $('input[name=amount]').val(data.amount_raw)
                    $('.amount-text').text(data.sub_total)
                    $('.discount-text').text(data.discount_amount)
                    $('.tax-text').text(data.tax_amount)
                }

                $('body').css('cursor', 'default')
                $('.custom-checkbox label').css('cursor', 'pointer')

                $('.payment-checkout-form .list_payment_method').load(window.location.href + ' .payment-checkout-form .list_payment_method > *', function() {
                    $checkoutButton.prop('disabled', false)
                    $(document).find('.payment-checkout-form .list_payment_method input[value="' + $selectedPaymentMethod + '"]').prop('checked', true).trigger('change')
                })
            },
            error: () => {
                $('body').css('cursor', 'default')
                $('.custom-checkbox label').css('cursor', 'pointer')
                $checkoutButton.prop('disabled', false)
            },
        })
    })

    $('.create-customer').on('change', 'input[name="register_customer"]', function (event) {
        const $formCreate = $('.form-create-customer-password')

        if (event.target.checked) {
            $formCreate.removeClass('d-none')
        } else {
            $formCreate.addClass('d-none')
        }
    })

    const refreshCoupon = () => {
        const services = []
        $('.service-item:checked').each((i, el) => {
            services[i] = $(el).val()
        })

        const foods = []
        $('.food-item:checked').each((i, el) => {
            foods[i] = $(el).val()
        })

        const slots = []
        $('input[name^="slots["][name$="[start_date]"]').each(function (i, el) {
            const start = $(el).val()
            const end = $(document).find(`input[name="slots[${i}][end_date]"]`).val()
            if (start && end) {
                slots.push({ start_date: start, end_date: end })
            }
        })

        const $checkoutButton = $(document).find('.payment-checkout-btn')
        const enableCheckout = () => $checkoutButton.prop('disabled', false)
        const disableCheckout = () => $checkoutButton.prop('disabled', true)

        disableCheckout()

        const $paymentMethodList = $(document).find('.payment-checkout-form .list_payment_method')
        const selectedPaymentMethod = $(document).find('.payment-checkout-form .list_payment_method input[name="payment_method"]:checked').val()
        const $couponBox = $(document).find('.order-detail-box').first()
        const refreshUrl = $couponBox.data('refresh-url')

        $.ajax({
            url: '/ajax/calculate-amount',
            type: 'GET',
            data: {
                room_id: $('input[name=room_id]').val(),
                slots,
                services,
                foods,
            },
            success: ({ error, message, data }) => {
                if (error) {
                    RiorelaxTheme.showError(message)

                    enableCheckout()

                    return
                }

                $('.total-amount-text').text(data.total_amount)
                $('input[name=amount]').val(data.amount_raw)
                $('.amount-text').text(data.sub_total)
                $('.discount-text').text(data.discount_amount)
                $('.tax-text').text(data.tax_amount)

                const paymentMethodsReload = $.Deferred()

                if ($paymentMethodList.length) {
                    $paymentMethodList.load(window.location.href + ' .payment-checkout-form .list_payment_method > *', function(response, status) {
                        if (status === 'error') {
                            paymentMethodsReload.reject()

                            return
                        }

                        $(document)
                            .find('.payment-checkout-form .list_payment_method input[value="' + selectedPaymentMethod + '"]')
                            .prop('checked', true)
                            .trigger('change')

                        paymentMethodsReload.resolve()
                    })
                } else {
                    paymentMethodsReload.resolve()
                }

                const couponDetailsReload = $.Deferred()

                if (refreshUrl) {
                    $.ajax({
                        url: refreshUrl,
                        type: 'GET',
                        data: {
                            coupon_code: $('input[name=coupon_hidden]').val() ?? $('input[name=coupon_code]').val(),
                        },
                        success: ({ error, message, data }) => {
                            if (error) {
                                RiorelaxTheme.showError(message)

                                couponDetailsReload.reject()

                                return
                            }

                            if ($couponBox.length) {
                                $couponBox.replaceWith(data)
                            } else {
                                $(document).find('.order-detail-box').first().html(data)
                            }

                            couponDetailsReload.resolve()
                        },
                        error: (error) => {
                            RiorelaxTheme.handleError(error)
                            couponDetailsReload.reject()
                        },
                    })
                } else {
                    couponDetailsReload.resolve()
                }

                $.when(paymentMethodsReload, couponDetailsReload).always(() => {
                    enableCheckout()
                })
            },
            error: (error) => {
                RiorelaxTheme.handleError(error)
                enableCheckout()
            },
        })
    }

    $(document)
        .on('click', '.toggle-coupon-form', () => $(document).find('.coupon-form').toggle('fast'))
        .on('click', '.apply-coupon-code', (e) => {
            e.preventDefault()

            const slots = []
            $('input[name^="slots["][name$="[start_date]"]').each(function (i, el) {
                const start = $(el).val()
                const end = $(document).find(`input[name="slots[${i}][end_date]"]`).val()
                if (start && end) {
                    slots.push({ start_date: start, end_date: end })
                }
            })





            const $button = $(e.currentTarget)
            const $couponInput = $('input[name=coupon_code]')
            const couponCode = ($couponInput.val() || '').trim()

            if (!couponCode.length) {
                RiorelaxTheme.showError('Please enter a coupon code.')

                return
            }

            $couponInput.val(couponCode)

            $.ajax({
                url: $button.data('url'),
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    coupon_code: couponCode,
                },
                beforeSend: () => {
                    $button.addClass('button-loading')
                },
                success: ({ error, message, data }) => {
                    if (error) {
                        RiorelaxTheme.showError(message)

                        return
                    }

                    const appliedCoupon = data?.coupon_code ?? couponCode

                    let successMessage = message

                    if (appliedCoupon) {
                        if (!successMessage) {
                            successMessage = `Applied coupon "${appliedCoupon}" successfully!`
                        } else if (successMessage.includes('""')) {
                            successMessage = successMessage.replace('""', `"${appliedCoupon}"`)
                        } else if (successMessage.includes(':code')) {
                            successMessage = successMessage.replace(':code', appliedCoupon)
                        }
                    }

                    RiorelaxTheme.showSuccess(successMessage ?? 'Coupon applied successfully!')
                    refreshCoupon()
                },
                error: (error) => {
                    RiorelaxTheme.handleError(error)
                },
                complete: () => {
                    $button.removeClass('button-loading')
                }
            })
        })
        .on('click', '.remove-coupon-code', (e) => {
            e.preventDefault()

            const $button = $(e.currentTarget)

            $.ajax({
                url: $button.data('url'),
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: () => {
                    $button.addClass('button-loading')
                },
                success: ({ message, error }) => {
                    if (error) {
                        RiorelaxTheme.showError(message)

                        return
                    }

                    RiorelaxTheme.showSuccess(message)

                    refreshCoupon()
                },
                error: (error) => {
                    RiorelaxTheme.handleError(error)
                },
                complete: () => {
                    $button.removeClass('button-loading')
                },
            })
        })
})

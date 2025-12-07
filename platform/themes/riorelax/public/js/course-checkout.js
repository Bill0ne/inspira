$(document).ready(function () {

    let isRefreshingCoupon = false;
    let pendingCouponRefresh = false;

    const toggleIncompatibleActions = () => {
        const $couponBox = $('#courseCouponBox');
        const $cardSection = $('[data-bb-customer-card-section]');

        const state = window.CheckoutState || {};
        const hasCard = !!(state.card && state.card.id);
        const hasCoupon = !!((state.coupon && state.coupon.code) || state.coupon_code);

        if ($couponBox.length) {
            $couponBox.toggleClass('d-none', hasCard);
        }

        if ($cardSection.length) {
            $cardSection.toggleClass('d-none', hasCoupon);
        }
    };

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
            .done((response = {}) => {
                const error = response.error;
                const message = response.message;
                const data =
                    response && response.data && typeof response.data === 'object'
                        ? response.data
                        : {};

                if (error) {
                    if (window.RiorelaxTheme) {
                        window.RiorelaxTheme.showError(message);
                    }
                    finishRefresh();
                    return;
                }

                const currentState = window.CheckoutState || {};
                const mergedState = $.extend(true, {}, currentState, data);
                const existingCouponCode =
                    (currentState.coupon && currentState.coupon.code) ||
                    currentState.coupon_code ||
                    '';
                const incomingCouponCode =
                    (mergedState.coupon && mergedState.coupon.code) ||
                    mergedState.coupon_code ||
                    '';
                const couponCode = incomingCouponCode || existingCouponCode;

                if (couponCode) {
                    mergedState.coupon = mergedState.coupon || {};
                    mergedState.coupon.code = couponCode;
                    mergedState.coupon_code = couponCode;
                }

                if (
                    window.CheckoutCommerce &&
                    typeof window.CheckoutCommerce.setState === 'function'
                ) {
                    window.CheckoutCommerce.setState(mergedState, 'course');
                } else {
                    window.CheckoutState = mergedState || {};
                    if (typeof window.renderCheckoutUI === 'function') {
                        window.renderCheckoutUI('course');
                    }
                }

                toggleIncompatibleActions();

                const reloadPromise =
                    window.CheckoutCommerce &&
                    typeof window.CheckoutCommerce.reloadPaymentList === 'function'
                        ? window.CheckoutCommerce.reloadPaymentList('course')
                        : $.Deferred().resolve();

                reloadPromise.always(() => {
                    finishRefresh();
                    $(document).trigger('customer-card.totals-updated', data);
                });

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

    toggleIncompatibleActions();
});

$(document).ready(function () {

    let isRefreshingCoupon = false;
    let pendingCouponRefresh = false;

    const $document = $(document);

    const findCouponBox = () => $('#courseCouponBox');
    const findCardSection = () => $('[data-bb-customer-card-section]');

    const resolveCouponInputValue = () => {
        const $couponBox = findCouponBox();
        if (!$couponBox.length) {
            return '';
        }

        const hiddenValue = ($couponBox.find('input[name=coupon_hidden]').val() || '').trim();
        if (hiddenValue) {
            return hiddenValue;
        }

        return ($couponBox.find('input[name=coupon_code]').val() || '').trim();
    };

    const syncCardSectionVisibility = () => {
        const $cardSection = findCardSection();
        if (!$cardSection.length) {
            return;
        }

        const $couponBox = findCouponBox();
        const hasCouponFeedback = $couponBox.find('.coupon-feedback').length > 0;
        const hasCouponValue = !!resolveCouponInputValue();
        const shouldHideCard = hasCouponFeedback || hasCouponValue;

        $cardSection.toggleClass('d-none', shouldHideCard);
    };

    const bindCouponInputWatcher = () => {
        $document
            .off('input.courseCoupon change.courseCoupon', '#courseCouponBox input[name=coupon_code]')
            .on('input.courseCoupon change.courseCoupon', '#courseCouponBox input[name=coupon_code]', syncCardSectionVisibility);
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

                if (
                    window.CheckoutCommerce &&
                    typeof window.CheckoutCommerce.setState === 'function'
                ) {
                    window.CheckoutCommerce.setState(data, 'course');
                } else {
                    window.CheckoutState = data || {};
                    if (typeof window.renderCheckoutUI === 'function') {
                        window.renderCheckoutUI('course');
                    }
                }

                const reloadPromise =
                    window.CheckoutCommerce &&
                    typeof window.CheckoutCommerce.reloadPaymentList === 'function'
                        ? window.CheckoutCommerce.reloadPaymentList('course')
                        : $.Deferred().resolve();

                reloadPromise.always(() => {
                    finishRefresh();
                    $(document).trigger('customer-card.totals-updated', data);
                    syncCardSectionVisibility();
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

    bindCouponInputWatcher();
    syncCardSectionVisibility();

    // ⚠️ WICHTIG:
    // KEINE eigenen Event-Handler mehr für:
    //  - .toggle-coupon-form
    //  - .apply-coupon-code
    //  - .remove-coupon-code
    // Das übernimmt checkout-commerce.js exklusiv.

    // Reaktion auf Kundenkarten-Ereignisse:
    $(document).on('customer-card.applied customer-card.removed', () => {
        syncCardSectionVisibility();
        refreshCourseCoupon();
    });

    // Reaktion auf Coupon-Ereignisse aus checkout-commerce.js:
    $(document).on('coupon.applied coupon.removed', function () {
        syncCardSectionVisibility();
        refreshCourseCoupon();
    });
});

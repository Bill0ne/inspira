$(document).ready(function () {

    const renderFromState = () => {
        if (window.CheckoutCommerce && typeof window.CheckoutCommerce.renderCheckoutUI === 'function') {
            window.CheckoutCommerce.renderCheckoutUI('course');
        } else if (typeof window.renderCheckoutUI === 'function') {
            window.renderCheckoutUI('course');
        }
    };

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

    const refreshCourseCheckout = () => {
        renderFromState();
        toggleIncompatibleActions();

        if (window.CheckoutCommerce && typeof window.CheckoutCommerce.reloadPaymentList === 'function') {
            return window.CheckoutCommerce.reloadPaymentList('course');
        }

        return $.Deferred().resolve();
    };

    window.RioRelaxCourseCheckout = window.RioRelaxCourseCheckout || {};
    window.RioRelaxCourseCheckout.refreshCourseCoupon = refreshCourseCheckout;

    $(document).off('.courseCheckout');
    $(document)
        .on(
            'customer-card.applied.courseCheckout customer-card.removed.courseCheckout coupon.applied.courseCheckout coupon.removed.courseCheckout',
            function () {
                refreshCourseCheckout();
            }
        );

    toggleIncompatibleActions();
});

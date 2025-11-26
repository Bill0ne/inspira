$(document).ready(function () {

    const renderFromState = () => {
        if (window.CheckoutCommerce && typeof window.CheckoutCommerce.renderCheckoutUI === 'function') {
            window.CheckoutCommerce.renderCheckoutUI('course');
        } else if (typeof window.renderCheckoutUI === 'function') {
            window.renderCheckoutUI('course');
        }
    };

    const refreshCourseCheckout = () => {
        renderFromState();

        if (window.CheckoutCommerce && typeof window.CheckoutCommerce.reloadPaymentList === 'function') {
            return window.CheckoutCommerce.reloadPaymentList('course');
        }

        return $.Deferred().resolve();
    };

    window.RioRelaxCourseCheckout = window.RioRelaxCourseCheckout || {};
    window.RioRelaxCourseCheckout.refreshCourseCoupon = refreshCourseCheckout;

    $(document).off('.courseCheckout');
    $(document).on(
        'customer-card.applied.courseCheckout customer-card.removed.courseCheckout coupon.applied.courseCheckout coupon.removed.courseCheckout',
        function () {
            refreshCourseCheckout();
        }
    );
});

$(function () {
    'use strict';

    const hydrateWidgets = () => {
        if (typeof window.BDashboard === 'undefined') {
            setTimeout(hydrateWidgets, 100);

            return;
        }

        const $widgetItems = $('[data-bb-toggle="widgets-list"] .widget-item');

        if (! $widgetItems.length) {
            return;
        }

        $widgetItems.each((index, element) => {
            const $item = $(element);
            const $content = $item.find('.widget-content.personal-dashboard-widget');

            if (! $content.length) {
                return;
            }

            const url = $item.data('url');

            if (! url) {
                return;
            }

            window.BDashboard.loadWidget($content, url);
        });
    };

    hydrateWidgets();
});

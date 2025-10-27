$(function () {
    'use strict';

    if (typeof window.BDashboard === 'undefined') {
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
});

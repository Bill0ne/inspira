$(document).ready(() => {
    $(document).on('click', '#is_change_password', (event) => {
        if ($(event.currentTarget).is(':checked')) {
            $('input[type=password]').closest('.form-group').removeClass('hidden').fadeIn();
        } else {
            $('input[type=password]').closest('.form-group').addClass('hidden').fadeOut();
        }
    });

    const navToggleSelector = '.customer-nav-toggle';
    const navSelector = '#customer-nav';

    const syncNavState = () => {
        const $toggle = $(navToggleSelector);
        const $nav = $(navSelector);

        if (!$toggle.length || !$nav.length) {
            return;
        }

        if (window.matchMedia('(min-width: 768px)').matches) {
            $nav.removeClass('is-open');
            $toggle.attr('aria-expanded', 'false');
            return;
        }

        $toggle.attr('aria-expanded', $nav.hasClass('is-open') ? 'true' : 'false');
    };

    $(document).on('click', navToggleSelector, (event) => {
        event.preventDefault();

        const $toggle = $(event.currentTarget);
        const target = $toggle.data('target') || navSelector;
        const $nav = $(target);

        if (!$nav.length) {
            return;
        }

        const isOpen = !$nav.hasClass('is-open');

        $nav.toggleClass('is-open', isOpen);
        $toggle.attr('aria-expanded', isOpen ? 'true' : 'false');
    });

    $(window).on('resize', syncNavState);

    syncNavState();
});

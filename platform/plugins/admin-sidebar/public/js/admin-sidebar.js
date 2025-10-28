(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        var adminToggle = document.querySelector('.menu-section--admin > a.nav-link');

        if (! adminToggle) {
            return;
        }

        var updateState = function () {
            var expanded = adminToggle.getAttribute('aria-expanded') === 'true';

            if (expanded) {
                adminToggle.classList.add('is-open');
            } else {
                adminToggle.classList.remove('is-open');
            }
        };

        adminToggle.addEventListener('shown.bs.dropdown', updateState);
        adminToggle.addEventListener('hidden.bs.dropdown', updateState);

        updateState();
    });
})();

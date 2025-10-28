(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        var groupsToOpen = document.querySelectorAll('.menu-section--management, .menu-section--website');

        groupsToOpen.forEach(function (group) {
            var toggle = group.querySelector('a.nav-link.dropdown-toggle');
            var menu = group.querySelector('.dropdown-menu');

            if (! toggle || ! menu) {
                return;
            }

            if (toggle.closest('.menu-section') !== group || menu.closest('.menu-section') !== group) {
                return;
            }

            if (toggle.getAttribute('aria-expanded') === 'true' || menu.classList.contains('show')) {
                return;
            }

            if (window.bootstrap && typeof window.bootstrap.Dropdown === 'function') {
                var dropdown = window.bootstrap.Dropdown.getOrCreateInstance(toggle);

                dropdown.show();
            } else {
                toggle.classList.add('show');
                toggle.setAttribute('aria-expanded', 'true');
                menu.classList.add('show');
            }
        });

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

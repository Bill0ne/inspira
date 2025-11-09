<nav class="navbar navbar-expand-lg bg-body-tertiary menu-mobile d-lg-none" role="navigation" aria-label="{{ __('Mobile navigation') }}">
    <div class="collapse navbar-collapse" id="menu-mobile-nav" aria-expanded="false" aria-hidden="true">
        <div class="menu">
            <div class="menu-title">
                <span>{{ __('Menu') }}</span>
            </div>
            {!! Menu::renderMenuLocation('main-menu', [
                'view' => 'menu-mobile',
                'options' => ['class' => 'navbar-nav mb-2 mb-lg-0 me-3 ms-3'],
            ]) !!}
            @if (is_plugin_active('language') && ($supportedLocales = Language::getSupportedLocales()) && count($supportedLocales) > 1)
                <div class="menu-title mt-20">
                    <span>{{ __('Languages') }}</span>
                </div>
                <ul class="navbar-nav mb-2 mb-lg-0 me-3 ms-3">
                    {!! Theme::partial('language-switcher-mobile') !!}
                </ul>
            @endif
            <div class="menu-title mt-20">
                <span>{{ __('Currencies') }}</span>
            </div>
            <ul class="navbar-nav mb-2 mb-lg-0 me-3 ms-3">
                {!! Theme::partial('currency-switcher-mobile') !!}
            </ul>

            @if(is_plugin_active('hotel'))
                <div class="menu-title mt-20">
                    <span>{{ __('Account') }}</span>
                </div>
                <ul class="navbar-nav mb-2 mb-lg-0 me-3 ms-3">
                    <li><a href="{{ route('customer.login') }}">{{ __('Login') }}</a></li>
                    <li><a href="{{ route('customer.register') }}">{{ __('Register') }}</a></li>
                </ul>
            @endif
        </div>
    </div>
    <div class="menu-mobile__overlay" data-menu-mobile-overlay></div>
</nav>

@php
    Theme::asset()->container('footer')->writeContent('menu-mobile-controller', <<<'HTML'
<script>
document.addEventListener('DOMContentLoaded', function () {
    var mobileMenu = document.querySelector('.menu-mobile');
    var menuCollapse = document.getElementById('menu-mobile-nav');
    var overlay = mobileMenu ? mobileMenu.querySelector('[data-menu-mobile-overlay]') : null;

    if (!mobileMenu || !menuCollapse || typeof bootstrap === 'undefined' || !bootstrap.Collapse) {
        return;
    }

    var toggleBodyState = function (isOpen) {
        document.body.classList.toggle('menu-mobile-open', isOpen);
        mobileMenu.classList.toggle('menu-mobile--open', isOpen);
        menuCollapse.setAttribute('aria-hidden', String(!isOpen));
        menuCollapse.setAttribute('aria-expanded', String(isOpen));
    };

    menuCollapse.addEventListener('shown.bs.collapse', function () {
        toggleBodyState(true);
    });

    menuCollapse.addEventListener('hidden.bs.collapse', function () {
        toggleBodyState(false);
    });

    if (overlay) {
        overlay.addEventListener('click', function () {
            var collapseInstance = bootstrap.Collapse.getInstance(menuCollapse);
            if (collapseInstance) {
                collapseInstance.hide();
            }
        });
    }
});
</script>
HTML);
@endphp

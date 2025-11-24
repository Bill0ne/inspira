@php
    Theme::set('pageTitle', 'Profile');

    $user = auth('customer')->user();
    $currentRoute = Route::currentRouteName();
    $navigation = [
        [
            'label' => __('Overview'),
            'route' => 'customer.overview',
            'icon' => 'fal fa-circle-user',
            'pattern' => 'customer.overview',
        ],
        [
            'label' => __('My Bookings'),
            'route' => 'customer.bookings',
            'icon' => 'fal fa-calendar-check',
            'pattern' => ['customer.bookings', 'customer.course-bookings'],
        ],
        [
            'label' => trans('plugins/hotel::customer-card.name'),
            'route' => 'customer.cards',
            'icon' => 'fal fa-credit-card',
            'pattern' => 'customer.cards',
        ],
        [
            'label' => __('Profile'),
            'route' => 'customer.edit-account',
            'icon' => 'fal fa-id-card',
            'pattern' => 'customer.edit-account',
        ],
        [
            'label' => __('Change password'),
            'route' => 'customer.change-password',
            'icon' => 'fal fa-lock',
            'pattern' => 'customer.change-password',
        ],
    ];

    if (HotelHelper::isReviewEnabled()) {
        $navigation[] = [
            'label' => __('My Reviews'),
            'route' => 'customer.reviews',
            'icon' => 'fal fa-star',
            'pattern' => 'customer.reviews',
        ];
    }

    $navigation[] = [
        'label' => __('Logout'),
        'route' => 'customer.logout',
        'icon' => 'fal fa-sign-out',
        'pattern' => null,
        'is_logout' => true,
    ];

    $primaryRoutes = ['customer.overview', 'customer.bookings', 'customer.cards'];
    $primaryNavigation = [];
    $overflowNavigation = [];
    $overflowHasActive = false;

    $isNavItemActive = static function (array $item) use ($currentRoute) {
        $patterns = $item['pattern'] ? (array) $item['pattern'] : [$item['route']];
        foreach ($patterns as $pattern) {
            if (\Illuminate\Support\Str::startsWith($currentRoute, $pattern)) {
                return true;
            }
        }
        return false;
    };

    foreach ($navigation as $item) {
        $item['is_active'] = $isNavItemActive($item);
        if (in_array($item['route'], $primaryRoutes, true)) {
            $primaryNavigation[] = $item;
        } else {
            $overflowNavigation[] = $item;
            if ($item['is_active']) {
                $overflowHasActive = true;
            }
        }
    }
@endphp

<style>
/* --- RESET THEME CENTERING --- */
.customer-page,
.customer-shell {
    width: 100% !important;
    max-width: 100% !important;
    text-align: left !important;
}

/* --- HEADER BASE --- */
.customer-header {
    display: flex !important;
    flex-direction: row !important;
    justify-content: space-between !important;
    align-items: center !important;
    width: 100% !important;
    background: #fff;
    border-radius: 12px;
    padding: 16px 24px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.05);
    margin: 0 auto;
    gap: 20px;
    min-height: 70px;
}

/* --- LEFT SIDE --- */
.customer-header-left {
    display: flex;
    align-items: center;
    gap: 10px;
    flex: 0 0 auto;
}
.customer-header-left .avatar {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    object-fit: cover;
}
.customer-header-left .customer-name {
    font-size: 14px;
    font-weight: 500;
    color: #333;
    white-space: nowrap;
}

/* --- RIGHT SIDE --- */
.customer-header-right {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 24px;
    flex: 1 1 auto;
    position: relative;
}
.customer-header-right .nav-link {
    position: relative;
    color: #333;
    font-weight: 500;
    font-size: 13px;
    text-decoration: none;
    transition: color 0.3s ease;
}
.customer-header-right .nav-link.active,
.customer-header-right .nav-link:hover {
    color: #578E88;
}
.customer-header-right .nav-link.active::after {
    content: "";
    position: absolute;
    bottom: -6px;
    left: 0;
    right: 0;
    height: 2px;
    background: #578E88;
    border-radius: 2px;
}
.customer-header-right button {
    background: none;
    border: none;
    font-size: 18px;
    color: #333;
    cursor: pointer;
}
.customer-header-right .dropdown {
    display: none;
    position: absolute;
    right: 0;
    top: 120%;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    min-width: 160px;
    overflow: hidden;
    z-index: 10;
}
.customer-header-right.open .dropdown {
    display: block;
}
.customer-header-right .dropdown a {
    display: block;
    padding: 10px 16px;
    font-size: 13px;
    color: #333;
    text-decoration: none;
    transition: background 0.2s;
}
.customer-header-right .dropdown a:hover {
    background: #f3f3f3;
    color: #578E88;
}
.customer-header-right .dropdown a.logout {
    color: #a33;
}

/* --- MOBILE --- */
@media (max-width: 768px) {
    .customer-header {
        display: grid;
        grid-template-columns: 1fr;
        grid-row-gap: 12px;
        align-items: center;
        text-align: center;
    }
    .customer-header-right {
        width: 100%;
        justify-content: center;
        flex-wrap: wrap;
        gap: 10px 16px;
    }
    .customer-header-right .nav-link {
        font-size: 12px;
        padding: 4px 8px;
    }
    .customer-header-right button {
        font-size: 16px;
        padding: 4px 8px;
    }
    .customer-header-right .dropdown {
        width: 100%;
        position: static;
        box-shadow: none;
        border-top: 1px solid #eee;
        border-radius: 0;
        margin-top: 4px;
    }
}
</style>

<div class="customer-page crop-avatar inspira-customer">
    <div class="customer-shell">
        <header class="customer-header" aria-label="{{ __('Account overview') }}">
            {{-- LEFT SIDE: Avatar + Name --}}
            <div class="customer-header-left">
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="avatar">
                <span class="customer-name">{{ $user->name }}</span>
            </div>

            {{-- RIGHT SIDE: Navigation + Dropdown --}}
            <div class="customer-header-right" id="navDropdown">
                @foreach ($primaryNavigation as $item)
                    <a href="{{ route($item['route']) }}" 
                       class="nav-link {{ $item['is_active'] ? 'active' : '' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach

                <button type="button"
                        aria-label="{{ __('More options') }}"
                        onclick="document.getElementById('navDropdown').classList.toggle('open')">
                    <i class="fal fa-ellipsis-h"></i>
                </button>

                <div class="dropdown" id="dropdown-menu">
                    @foreach ($overflowNavigation as $item)
                        <a href="{{ route($item['route']) }}" 
                           class="{{ $item['is_logout'] ?? false ? 'logout' : '' }}">
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        </header>

        <div class="customer-content">
            <div class="customer-content-inner">
                @yield('content')
            </div>
        </div>
    </div>

    {{-- Avatar Upload Modal --}}
    <div class="modal fade" id="avatar-modal" tabindex="-1" role="dialog" aria-labelledby="avatar-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form class="avatar-form" method="post" action="{{ route('customer.avatar') }}" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h4 class="modal-title" id="avatar-modal-label"><i class="til_img"></i><strong>{{ __('Profile Image') }}</strong></h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="avatar-body">
                            <div class="avatar-upload">
                                <input class="avatar-src" name="avatar_src" type="hidden" />
                                <input class="avatar-data" name="avatar_data" type="hidden" />
                                @csrf
                                <label for="avatarInput">{{ __('New image') }}</label>
                                <input class="avatar-input" id="avatarInput" name="avatar_file" type="file" />
                            </div>

                            <div class="loading" style="display: none;" tabindex="-1" role="img" aria-label="{{ __('Loading') }}"></div>

                            <div class="row">
                                <div class="col-md-9">
                                    <div class="avatar-wrapper"></div>
                                    <div class="error-message text-danger" style="display: none;"></div>
                                </div>
                                <div class="col-md-3 avatar-preview-wrapper">
                                    <div class="avatar-preview preview-lg"></div>
                                    <div class="avatar-preview preview-md"></div>
                                    <div class="avatar-preview preview-sm"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button class="btn btn-primary avatar-save" type="submit">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

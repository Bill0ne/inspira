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
        [
            'label' => __('My Bookings'),
            'route' => 'customer.bookings',
            'icon' => 'fal fa-calendar-check',
            'pattern' => ['customer.bookings', 'customer.course-bookings'],
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
@endphp

<div class="customer-page crop-avatar inspira-customer">
    <div class="customer-shell">
        <header class="customer-header" aria-label="{{ __('Account overview') }}">
            <div class="customer-header-main">
                <form id="avatar-upload-form" class="customer-header-avatar" enctype="multipart/form-data" action="javascript:void(0)" onsubmit="return false">
                    <div class="avatar-upload-container">
                        <div id="account-avatar" class="customer-avatar-frame">
                            <div class="profile-image custom-avatar-master">
                                <div class="avatar-view mt-card-avatar">
                                    <img class="br2" src="{{ $user->avatar_url }}" alt="{{ $user->name }}" />
                                </div>
                                <i class="fa fa-pencil avatar-view"></i>
                            </div>
                        </div>
                        <div id="print-msg" class="text-danger hidden"></div>
                    </div>
                </form>

                <div class="customer-header-meta">
                    <p class="customer-header-name">{{ $user->name }}</p>
                </div>
            </div>

            <nav id="customer-nav" class="customer-tab-nav" aria-label="{{ __('Account navigation') }}">
                <ul class="customer-nav">
                    @foreach ($navigation as $item)
                        @php
                            $patterns = $item['pattern'] ? (array) $item['pattern'] : [];
                            $isActive = false;

                            foreach ($patterns as $pattern) {
                                if (\Illuminate\Support\Str::startsWith($currentRoute, $pattern)) {
                                    $isActive = true;
                                    break;
                                }
                            }
                        @endphp

                        <li class="customer-nav-item {{ $isActive ? 'is-active' : '' }} {{ $item['is_logout'] ?? false ? 'is-logout' : '' }}">
                            <a class="customer-nav-link" href="{{ route($item['route']) }}">
                                <span class="customer-nav-icon" aria-hidden="true">
                                    <i class="{{ $item['icon'] }}"></i>
                                </span>
                                <span class="customer-nav-text">{{ $item['label'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>
        </header>

        <div class="customer-content">
            <div class="customer-content-inner">
                @yield('content')
            </div>
        </div>
    </div>

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

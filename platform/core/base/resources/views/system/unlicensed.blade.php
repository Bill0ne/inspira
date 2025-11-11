<x-core::layouts.base
    body-class="d-flex flex-column"
    :body-attributes="['data-bs-theme' => 'dark']"
>
    @section('title', trans('core/base::system.license.title'))

    <div class="page page-center">
        <div class="container container-tight py-4">
            <div class="text-center mb-4">
                @include('core/base::partials.logo')
            </div>

            <x-core::card size="md">
                <x-core::card.body>
                    <h2 class="mb-3 text-center">{{ trans('core/base::system.license.title') }}</h2>

                    <p class="text-secondary mb-4">
                        {{ trans('core/base::system.license.description') }}
                    </p>

                    <ul class="list-unstyled space-y">
                        <li class="row g-2">
                            <span class="col-auto">
                                <x-core::icon name="ti ti-check" class="me-1 text-success" />
                            </span>
                            <span class="col">
                                <strong class="d-block">{{ trans('core/base::system.license.benefits.updates.title') }}</strong>
                                <span class="d-block text-secondary">{{ trans('core/base::system.license.benefits.updates.description') }}</span>
                            </span>
                        </li>

                        <li class="row g-2">
                            <span class="col-auto">
                                <x-core::icon name="ti ti-check" class="me-1 text-success" />
                            </span>
                            <span class="col">
                                <strong class="d-block">{{ trans('core/base::system.license.benefits.support.title') }}</strong>
                                <span class="d-block text-secondary">{{ trans('core/base::system.license.benefits.support.description') }}</span>
                            </span>
                        </li>

                        <li class="row g-2">
                            <span class="col-auto">
                                <x-core::icon name="ti ti-check" class="me-1 text-success" />
                            </span>
                            <span class="col">
                                <strong class="d-block">{{ trans('core/base::system.license.benefits.security.title') }}</strong>
                                <span class="d-block text-secondary">{{ trans('core/base::system.license.benefits.security.description') }}</span>
                            </span>
                        </li>
                    </ul>
                </x-core::card.body>

                <x-core::card.footer class="border-top">
                    <div class="mb-2">
                        <x-core::button
                            color="primary"
                            class="w-100"
                            data-bs-toggle="modal"
                            data-bs-target="#quick-activation-license-modal"
                            aria-label="{{ trans('core/base::system.license.activate_aria') }}"
                        >
                            {{ trans('core/base::system.license.activate_button') }}
                        </x-core::button>
                    </div>

                    <div>
                        <form action="{{ route('unlicensed.skip') }}" method="POST">
                            @csrf

                            @if ($redirectUrl)
                                <input type="hidden" name="redirect_url" value="{{ $redirectUrl }}">
                            @endif

                            <x-core::button type="submit" class="w-100" color="link" size="sm">
                                {{ trans('core/base::system.license.skip_button') }}
                            </x-core::button>
                        </form>
                    </div>
                </x-core::card.footer>
            </x-core::card>
        </div>
    </div>

    @include('core/base::system.partials.license-activation-modal')
</x-core::layouts.base>

@php($userName = $user?->name ?: config('core.base.general.site_name'))

<div class="personal-dashboard-greeting position-relative overflow-hidden rounded-4 text-white" style="background: linear-gradient(135deg, #4f46e5 0%, #1d4ed8 45%, #0ea5e9 100%);">
    <div class="position-absolute top-0 end-0 opacity-25 d-none d-md-block" style="width: 14rem; height: 14rem; background: radial-gradient(circle, rgba(255,255,255,0.25) 0%, rgba(255,255,255,0) 70%); transform: translate(35%, -35%);"></div>

    <div class="position-relative p-4 p-lg-5">
        <span class="badge bg-white text-primary-emphasis fw-semibold text-uppercase mb-3">
            {{ trans('plugins/personal-dashboard::widgets.greeting_badge') }}
        </span>

        <h2 class="fw-bold display-6 mb-2">
            {{ trans('plugins/personal-dashboard::widgets.greeting_heading', ['name' => $userName]) }}
        </h2>

        <p class="fs-6 text-white-50 mb-4">
            {{ trans('plugins/personal-dashboard::widgets.greeting_message') }}
        </p>

        <div class="d-flex align-items-center gap-3 flex-wrap">
            <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center shadow" style="width: 3.75rem; height: 3.75rem;">
                <i class="ti ti-user-heart fs-3"></i>
            </div>

            <div class="text-white">
                <div class="text-uppercase small fw-semibold text-white-50">{{ trans('plugins/personal-dashboard::widgets.current_user') }}</div>
                <div class="fs-4 fw-bold">{{ $userName }}</div>
            </div>
        </div>
    </div>
</div>

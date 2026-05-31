@php(Theme::set('pageTitle', trans('plugins/community::community.public.title')))

<section class="container community-page mt-5 mb-5">
    <div class="text-center mb-5">
        <h1 class="mb-2">{{ trans('plugins/community::community.public.title') }}</h1>
        <p class="text-muted">{{ trans('plugins/community::community.public.subtitle') }}</p>
    </div>

    @if ($members->isEmpty())
        <p class="text-center text-muted">{{ __('No community members yet.') }}</p>
    @else
        <div class="row g-4">
            @foreach ($members as $member)
                <div class="col-12 col-sm-6 col-lg-4">
                    {!! Theme::partial('community.card', ['member' => $member]) !!}
                </div>
            @endforeach
        </div>
    @endif
</section>

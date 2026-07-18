@php
    Theme::set('pageTitle', $member->name);

    $customer = $member->customer;
    $rawAvatar = $customer ? ($customer->getAttributes()['avatar'] ?? null) : null;
    $photo = $member->photo ?: $rawAvatar;
    $image = $photo
        ? RvMedia::getImageUrl($photo, null, false, RvMedia::getDefaultImage())
        : RvMedia::getImageUrl('default-room.jpg', null, false, RvMedia::getDefaultImage());

    /* === Zitat sauber normalisieren: umschließende (auch doppelte) Anführungszeichen entfernen === */
    $quote = trim((string) $member->quote);
    $quote = preg_replace('/^[\s"“”„»«\'‘’]+|[\s"“”„»«\'‘’]+$/u', '', $quote);
@endphp

<style>
    .community-member {
        --cm-green: #578E88;
        --cm-green-dark: #4B7C75;
        padding-top: 60px;
        padding-bottom: 80px;
    }
    .community-member__grid {
        display: grid;
        grid-template-columns: minmax(0, 420px) minmax(0, 1fr);
        gap: 48px;
        align-items: start;
    }
    .community-member__portrait {
        position: sticky;
        top: 100px;
    }
    .community-member__portrait img {
        width: 100%;
        border-radius: 18px;
        object-fit: cover;
        aspect-ratio: 4 / 5;
        background: #f2f2f2;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .10);
    }
    .community-member__name {
        font-size: 34px;
        line-height: 1.15;
        font-weight: 700;
        color: #2b2b2b;
        margin: 0 0 8px;
    }
    .community-member__role {
        display: inline-block;
        font-size: 15px;
        font-weight: 600;
        color: var(--cm-green);
        margin-bottom: 24px;
    }
    .community-member__quote {
        background: var(--cm-green);
        color: #fff;
        border-radius: 14px;
        padding: 24px 28px;
        margin: 0 0 28px;
        font-size: 19px;
        line-height: 1.55;
        font-style: italic;
        position: relative;
        overflow-wrap: anywhere;
    }
    .community-member__quote::before {
        content: "\201C";
        display: block;
        font-size: 48px;
        line-height: 0;
        margin: 18px 0 8px;
        opacity: .45;
        font-family: Georgia, 'Times New Roman', serif;
    }
    .community-member__desc {
        font-size: 16px;
        line-height: 1.75;
        color: #4a4a4a;
    }
    .community-member__desc p {
        margin: 0 0 16px;
    }
    .community-member__cta {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background: var(--cm-green);
        color: #fff;
        padding: 13px 28px;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 600;
        margin-top: 12px;
        transition: background .2s ease, transform .2s ease;
    }
    .community-member__cta:hover {
        background: var(--cm-green-dark);
        color: #fff;
        transform: translateY(-1px);
    }
    .community-member__back {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        font-weight: 500;
        color: var(--cm-green);
        margin-bottom: 28px;
    }
    @media (max-width: 991px) {
        .community-member__grid {
            grid-template-columns: 1fr;
            gap: 32px;
        }
        .community-member__portrait {
            position: static;
            max-width: 420px;
            margin: 0 auto;
        }
        .community-member__name {
            font-size: 28px;
        }
    }
</style>

<section class="community-member">
    <div class="container">
        <a href="{{ route('public.community') }}" class="community-member__back">
            <i class="fal fa-arrow-left"></i> {{ trans('plugins/community::community.public.title') }}
        </a>

        <div class="community-member__grid">
            <div class="community-member__portrait">
                <img src="{{ $image }}" alt="{{ $member->name }}" loading="lazy">
            </div>

            <div class="community-member__content">
                <h1 class="community-member__name">{{ $member->name }}</h1>

                @if ($member->short_description)
                    <span class="community-member__role">{{ $member->short_description }}</span>
                @endif

                @if ($quote)
                    <blockquote class="community-member__quote">{{ $quote }}</blockquote>
                @endif

                @if ($member->description)
                    <div class="community-member__desc">
                        {!! BaseHelper::clean($member->description) !!}
                    </div>
                @endif

                <a href="{{ url('contact') }}" class="community-member__cta">
                    <i class="fal fa-paper-plane"></i> {{ trans('plugins/community::community.public.contact_cta') }}
                </a>
            </div>
        </div>
    </div>
</section>

@php
    $customer = $member->customer;
    $rawAvatar = $customer ? ($customer->getAttributes()['avatar'] ?? null) : null;
    $photo = $member->photo ?: $rawAvatar;
@endphp

@once
    <style>
        .community-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, .07);
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .community-card__media {
            width: 100%;
            aspect-ratio: 4 / 3;
            overflow: hidden;
            background: #f1f1f1;
        }
        .community-card__media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .community-card__body {
            padding: 18px 20px 22px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .community-card__name {
            font-size: 1.15rem;
            font-weight: 600;
            margin: 0;
            color: #17463F;
        }
        .community-card__desc {
            margin: 0;
            color: #5b6b68;
            font-size: .95rem;
            line-height: 1.45;
        }
        .community-card__quote {
            margin: 4px 0 0;
            padding-left: 14px;
            border-left: 3px solid #578E88;
            color: #2f4f49;
            font-style: italic;
            font-size: .95rem;
        }
    </style>
@endonce

<div class="community-card">
    <div class="community-card__media">
        <img
            src="{{ RvMedia::getImageUrl($photo ?: null, 'medium', false, RvMedia::getDefaultImage()) }}"
            alt="{{ $member->name }}"
            loading="lazy"
        >
    </div>
    <div class="community-card__body">
        <h3 class="community-card__name">{{ $member->name }}</h3>
        @if ($member->short_description)
            <p class="community-card__desc">{{ $member->short_description }}</p>
        @endif
        @if ($member->quote)
            <blockquote class="community-card__quote">&ldquo;{{ $member->quote }}&rdquo;</blockquote>
        @endif
    </div>
</div>

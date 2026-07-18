@php
    $customer = $member->customer;
    $rawAvatar = $customer ? ($customer->getAttributes()['avatar'] ?? null) : null;
    $photo = $member->photo ?: $rawAvatar;
    $image = $photo
        ? RvMedia::getImageUrl($photo, 'medium', false, RvMedia::getDefaultImage())
        : RvMedia::getImageUrl('default-room.jpg', 'medium', false, RvMedia::getDefaultImage());

    /* === Zitat sauber normalisieren: umschließende Anführungszeichen EINMAL entfernen === */
    $quote = trim((string) $member->quote);
    $quote = preg_replace('/^[\s"“”„»«\'‘’]+|[\s"“”„»«\'‘’]+$/u', '', $quote);

    $memberUrl = $member->url;
@endphp

@once
    <style>
        /* === Inspira – Community Card kompakt (community-full Grid) === */
        .community-card--compact {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, .06);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 100%;
            cursor: pointer;
            transition: transform .25s ease, box-shadow .25s ease;
        }
        .community-card--compact:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, .08);
        }
        .community-card--compact .community-card__thumb {
            padding: 8px;
        }
        .community-card--compact .community-card__thumb img {
            width: 100%;
            border-radius: 10px;
            display: block;
            object-fit: cover;
            aspect-ratio: 1 / 1;
            background: #f2f2f2;
        }
        .community-card--compact .community-card__body {
            padding: 4px 12px 12px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            flex: 1 1 auto;
        }
        .community-card--compact .community-card__name {
            font-size: 14px;
            line-height: 18px;
            font-weight: 600;
            color: #414141;
            margin: 0;
        }
        .community-card--compact .community-card__role {
            font-size: 11.5px;
            line-height: 16px;
            color: #6C6C6C;
            margin: 0;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .community-card--compact .community-card__quote {
            background: #578E88;
            color: #fff;
            border-radius: 6px;
            padding: 8px 10px;
            margin: 2px 0 0;
            font-style: italic;
            font-size: 11.5px;
            line-height: 16px;
            overflow-wrap: anywhere;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
@endonce

<div
    class="community-card--compact"
    onclick="if (event.target.closest('a, button')) return; window.location='{{ $memberUrl }}'"
>
    <div class="community-card__thumb">
        <img src="{{ $image }}" alt="{{ $member->name }}" loading="lazy">
    </div>

    <div class="community-card__body">
        <h4 class="community-card__name">{{ $member->name }}</h4>

        @if ($member->short_description)
            <p class="community-card__role">{{ $member->short_description }}</p>
        @endif

        @if ($quote)
            <blockquote class="community-card__quote">{{ $quote }}</blockquote>
        @endif
    </div>
</div>

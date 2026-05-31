@php
    $customer = $member->customer;
    $rawAvatar = $customer ? ($customer->getAttributes()['avatar'] ?? null) : null;
    $photo = $member->photo ?: $rawAvatar;
    $image = $photo
        ? RvMedia::getImageUrl($photo, 'medium', false, RvMedia::getDefaultImage())
        : RvMedia::getImageUrl('default-room.jpg', 'medium', false, RvMedia::getDefaultImage());
@endphp

@once
    <style>
        /* === Inspira – Community Card (gleiches Design wie Room/Course Card) === */
        .community-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, .06);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 100%;
            transition: transform .25s ease, box-shadow .25s ease;
        }
        .community-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, .08);
        }
        .community-card__thumb {
            padding: 12px;
        }
        .community-card__thumb img {
            width: 100%;
            border-radius: 14px;
            display: block;
            object-fit: cover;
            aspect-ratio: 16 / 9;
            background: #f2f2f2;
        }
        .community-card__body {
            padding: 4px 16px 16px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .community-card__name {
            font-size: 16px;
            line-height: 20px;
            font-weight: 600;
            color: #414141;
            margin: 0;
        }
        .community-card__desc {
            font-size: 12px;
            line-height: 18px;
            color: #6C6C6C;
            margin: 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .community-card__quote {
            margin: 2px 0 0;
            padding-left: 12px;
            border-left: 3px solid #578E88;
            color: #2f4f49;
            font-style: italic;
            font-size: 12.5px;
            line-height: 18px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
@endonce

<div class="community-card">
    <div class="community-card__thumb">
        <img src="{{ $image }}" alt="{{ $member->name }}" loading="lazy">
    </div>
    <div class="community-card__body">
        <h4 class="community-card__name">{{ $member->name }}</h4>
        @if ($member->short_description)
            <p class="community-card__desc">{{ $member->short_description }}</p>
        @endif
        @if ($member->quote)
            <blockquote class="community-card__quote">&ldquo;{{ $member->quote }}&rdquo;</blockquote>
        @endif
    </div>
</div>

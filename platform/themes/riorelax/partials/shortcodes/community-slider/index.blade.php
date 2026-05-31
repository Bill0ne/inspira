@php
    $sliderId = 'community-slider-' . uniqid();
    $count = $members->count();
@endphp

<section class="community-slider-section py-5">
    <div class="container">
        @if (! empty($shortcode->title) || ! empty($shortcode->subtitle))
            <div class="text-center mb-4 community-slider-heading">
                @if (! empty($shortcode->subtitle))
                    <span class="community-slider-subtitle">{{ $shortcode->subtitle }}</span>
                @endif
                @if (! empty($shortcode->title))
                    <h2 class="community-slider-title">{{ $shortcode->title }}</h2>
                @endif
            </div>
        @endif

        <div class="community-slider-active" id="{{ $sliderId }}" data-count="{{ $count }}">
            @foreach ($members as $member)
                <div class="community-slide">
                    {!! Theme::partial('community.card', ['member' => $member]) !!}
                </div>
            @endforeach
        </div>
    </div>
</section>

@once
    <style>
        .community-slider-subtitle {
            display: block;
            color: #578E88;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .08em;
            font-size: .8rem;
            margin-bottom: 4px;
        }
        .community-slider-title {
            color: #17463F;
            font-weight: 700;
        }
        /* vertikaler Slider: etwas Abstand zwischen den gestapelten Karten */
        .community-slider-active .slick-slide {
            padding: 10px 6px;
        }
        .community-slider-active .slick-list {
            margin: 0 -6px;
        }
        .community-slider-active .slick-prev,
        .community-slider-active .slick-next {
            left: 50%;
            transform: translateX(-50%);
            z-index: 5;
        }
        .community-slider-active .slick-prev { top: -6px; }
        .community-slider-active .slick-next { top: auto; bottom: -34px; }
        /* vor der Slick-Initialisierung nur die erste Karte zeigen (kein FOUC) */
        .community-slider-active:not(.slick-initialized) .community-slide:nth-child(n + 2) {
            display: none;
        }
    </style>
@endonce

<script>
    (function () {
        var sel = '#{{ $sliderId }}'
        function init() {
            if (!window.jQuery || !window.jQuery.fn || !window.jQuery.fn.slick) {
                return window.setTimeout(init, 200)
            }
            var $s = window.jQuery(sel)
            if (!$s.length || $s.hasClass('slick-initialized')) return
            var count = parseInt($s.attr('data-count'), 10) || 1
            $s.slick({
                vertical: true,
                verticalSwiping: true,
                slidesToShow: Math.min(count, 3),
                slidesToScroll: 1,
                arrows: true,
                dots: false,
                infinite: count > 3,
                autoplay: count > 3,
                autoplaySpeed: 4500,
                prevArrow: '<button type="button" class="slick-prev"><i class="far fa-chevron-up"></i></button>',
                nextArrow: '<button type="button" class="slick-next"><i class="far fa-chevron-down"></i></button>',
                responsive: [
                    { breakpoint: 768, settings: { slidesToShow: Math.min(count, 2) } },
                    { breakpoint: 576, settings: { slidesToShow: 1 } },
                ],
            })
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init)
        } else {
            init()
        }
    })()
</script>

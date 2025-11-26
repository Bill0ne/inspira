@php
    $backgroundImage = $shortcode->background_image;
    $youtubeVideoId = $shortcode->youtube_video_id;
    $frameBackground = $youtubeVideoId && $backgroundImage ? RvMedia::getImageUrl($backgroundImage) : null;
@endphp

<section class="video-area intro-video-full pt-150 pb-150 p-relative">

    <div class="content-lines-wrapper2">
        <div class="content-lines-inner2">
            <div class="content-lines2"></div>
        </div>
    </div>

    <div class="container-fluid px-0">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="s-video-wrap intro-video-full__frame" @if($frameBackground) style="background-image:url('{{ $frameBackground }}')" @endif>
                    @if ($youtubeVideoId)
                        <div class="s-video-content intro-video-full__overlay">
                            <a href="https://www.youtube.com/watch?v={{ $youtubeVideoId }}" class="popup-video">
                                @php
                                    $buttonIcon = $shortcode->button_icon ?
                                            RvMedia::getImageUrl($shortcode->button_icon) :
                                            Theme::asset()->url('/images/play-button.png')
                                @endphp
                                <img src="{{ $buttonIcon }}" alt="{{ __('Button play') }}">
                            </a>
                        </div>
                    @elseif ($videoUrl = $shortcode->video_url)
                        <div class="s-video-content intro-video-full__overlay">
                            <video class="intro-video-full__video" controls
                                @if($shortcode->autoplay) autoplay muted @endif
                                @if($shortcode->loop) loop @endif
                                @if ($backgroundImage)
                                    poster="{{ RvMedia::getImageUrl($backgroundImage) }}"
                                @endif
                            >
                                <source src="{{ RvMedia::getImageUrl($videoUrl) }}" type="video/mp4">
                            </video>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="section-title center-align text-center">
                    @if($title = $shortcode->title)
                        <h2>{!! BaseHelper::clean($title) !!}</h2>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .intro-video-full {
        background: #0f0f0f;
    }

    .intro-video-full__frame {
        position: relative;
        overflow: hidden;
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.35);
        background-size: cover;
        background-position: center;
        max-width: 1320px;
        margin: 0 auto;
    }

    .intro-video-full__overlay {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }

    .intro-video-full__video {
        width: 100%;
        height: 100%;
        display: block;
        background: #000;
    }

    .intro-video-full .s-video-content img {
        width: 96px;
        height: 96px;
        filter: drop-shadow(0 8px 24px rgba(0, 0, 0, 0.35));
    }

    @media (max-width: 991.98px) {
        .intro-video-full__frame {
            border-radius: 12px;
        }

        .intro-video-full .section-title h2 {
            margin-top: 30px;
        }
    }
</style>

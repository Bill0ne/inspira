<section id="skill" class="skill-area p-relative fix" @if($backgroundColor = $shortcode->background_color) style="background: {{ $backgroundColor }};" @endif>
    @if($backgroundImage = $shortcode->background_image)
        <div class="animations-01">
            <img src="{{ RvMedia::getImageURL($backgroundImage) }}" alt="{{ $shortcode->title }}" />
        </div>
    @endif
    <div class="container">
        <div class="row justify-content-center align-items-center">
            <div class="col-lg-6 col-md-12 col-sm-12">
                <div class="skills-content s-about-content">
                    @if($shortcode->title || $shortcode->subtitle)
                        <div class="skills-title pb-20">
                            @if($subtitle = $shortcode->subtitle)
                                <h5>{{ $subtitle }}</h5>
                            @endif

                            @if($title = $shortcode->title)
                                <h2>
                                    {!! BaseHelper::clean($title) !!}
                                </h2>
                            @endif
                        </div>
                    @endif

                    @if($description = $shortcode->description)
                        <p>{!! BaseHelper::clean($description) !!}</p>
                    @endif
                    <div class="skills-content s-about-content mt-10">
                        <div class="skills">
                            @foreach($tabs as $tab)
                                @if($tab['title'])
                                    <div class="skill-item mb-10" style="display: flex; align-items: center; padding: 5px 0;">
                                        <svg class="check-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="flex-shrink: 0; margin-right: 12px;">
                                            <path d="M20 6L9 17L4 12" stroke="#EDBE83" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <span class="skill-text" style="font-size: 16px; font-weight: 500; color: #333; line-height: 1.2;">{{ $tab['title'] }}</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 col-sm-12 pr-30">
                @if($rightImage = $shortcode->right_image)
                    <div class="skills-img wow fadeInRight animated" data-animation="fadeInRight" data-delay=".4s">
                        <img src="{{ RvMedia::getImageURL($rightImage) }}" alt="{{ $shortcode->title }}" class="img" />
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
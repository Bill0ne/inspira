<section id="contact" class="contact-area after-none contact-bg  pb-90 p-relative fix" style="margin-top: -90px;">
    <div class="container">
        <!-- Kontaktformular oben mittig -->
        <div class="row justify-content-center mb-60">
            <div class="col-lg-8 col-md-10">
                <div class="contact-bg02">
                    @if($title = $shortcode->title)
                        <div class="section-title center-align mb-40 text-center wow fadeInDown animated" data-animation="fadeInDown" data-delay=".4s">
                            <h2>
                                {!! BaseHelper::clean($title) !!}
                            </h2>
                        </div>
                    @endif
                    {!! $form->renderForm() !!}
                </div>
            </div>
        </div>

        <!-- Kontaktdaten in einer Zeile mit Rahmen -->
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="row justify-content-center">
                    @if($shortcode->address_label || $shortcode->address_detail)
                        <div class="col-auto mb-30">
                            <div class="single-cta-box text-center p-4 wow fadeInUp animated" data-animation="fadeInDown animated" data-delay=".2s" style="border: 2px solid #578E88; border-radius: 15px; width: 275px; height: 275px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                @if($addressIcon = $shortcode->address_icon)
                                    <div class="cta-icon mb-3">
                                        <i class="{{ $addressIcon }}" style="color: #578E88; font-size: 32px;"></i>
                                    </div>
                                @endif

                                @if($addressLabel = $shortcode->address_label)
                                    <h5 class="mb-2">{{ $addressLabel }}</h5>
                                @endif

                                @if($addressDetail = $shortcode->address_detail)
                                    <p class="mb-0">
                                        {!! BaseHelper::clean($addressDetail) !!}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($shortcode->work_time_label || $shortcode->work_time_detail)
                        <div class="col-auto mb-30">
                            <div class="single-cta-box text-center p-4 wow fadeInUp animated" data-animation="fadeInDown animated" data-delay=".3s" style="border: 2px solid #578E88; border-radius: 15px; width: 275px; height: 275px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                @if($workTimeIcon = $shortcode->work_time_icon)
                                    <div class="cta-icon mb-3">
                                        <i class="{{ $workTimeIcon }}" style="color: #578E88; font-size: 32px;"></i>
                                    </div>
                                @endif

                                @if($workTimeLabel = $shortcode->work_time_label)
                                    <h5 class="mb-2">{{ $workTimeLabel }}</h5>
                                @endif

                                @if($workTimeDetail = $shortcode->work_time_detail)
                                    <p class="mb-0">
                                        {!! BaseHelper::clean($workTimeDetail) !!}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($shortcode->phone_label && $shortcode->phone_detail)
                        <div class="col-auto mb-30">
                            <div class="single-cta-box text-center p-4 wow fadeInUp animated" data-animation="fadeInDown animated" data-delay=".4s" style="border: 2px solid #578E88; border-radius: 15px; width: 275px; height: 275px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                @if($phoneIcon = $shortcode->phone_icon)
                                    <div class="cta-icon mb-3">
                                        <i class="{{ $phoneIcon }}" style="color: #578E88; font-size: 32px;"></i>
                                    </div>
                                @endif

                                @if($phoneLabel = $shortcode->phone_label)
                                    <h5 class="mb-2">{{ $phoneLabel }}</h5>
                                @endif

                                @if($phoneDetail = $shortcode->phone_detail)
                                    <p class="mb-0">
                                        {!! BaseHelper::clean($phoneDetail) !!}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($shortcode->email_label || $shortcode->email_detail)
                        <div class="col-auto mb-30">
                            <div class="single-cta-box text-center p-4 wow fadeInUp animated" data-animation="fadeInDown animated" data-delay=".5s" style="border: 2px solid #578E88; border-radius: 15px; width: 275px; height: 275px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                @if($emailIcon = $shortcode->email_icon)
                                    <div class="cta-icon mb-3">
                                        <i class="{{ $emailIcon }}" style="color: #578E88; font-size: 32px;"></i>
                                    </div>
                                @endif

                                @if($emailLabel = $shortcode->email_label)
                                    <h5 class="mb-2">{{ $emailLabel }}</h5>
                                @endif

                                @if($emailDetail = $shortcode->email_detail)
                                    <p class="mb-0">
                                        {!! BaseHelper::clean($emailDetail) !!}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
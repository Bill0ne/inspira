@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="widget meta-boxes">
                <div class="widget-title">
                    <h4>
                        <span>{{ trans('plugins/hotel::ical.documentation') }}</span>
                    </h4>
                </div>
                <div class="widget-body">
                    <div class="documentation">
                        <h2>{{ trans('plugins/hotel::ical.documentation_title') }}</h2>

                        <div class="section">
                            <h3>
                                <x-core::icon name="ti ti-info-circle" class="me-1" />
                                {{ trans('plugins/hotel::ical.what_is_ical') }}
                            </h3>
                            <p>{{ trans('plugins/hotel::ical.what_is_ical_desc') }}</p>
                        </div>

                        <div class="section">
                            <h3>
                                <x-core::icon name="ti ti-upload" class="me-1" />
                                {{ trans('plugins/hotel::ical.export_bookings') }}
                            </h3>
                            <p>{{ trans('plugins/hotel::ical.export_bookings_desc') }}</p>

                            <div class="steps">
                                <div class="step">
                                    <div class="step-number">
                                        <x-core::badge label="1" color="primary" />
                                    </div>
                                    <div class="step-content">
                                        <h4>{{ trans('plugins/hotel::ical.export_step1_title') }}</h4>
                                        <p>{{ trans('plugins/hotel::ical.export_step1_desc') }}</p>
                                    </div>
                                </div>

                                <div class="step">
                                    <div class="step-number">
                                        <x-core::badge label="2" color="primary" />
                                    </div>
                                    <div class="step-content">
                                        <h4>{{ trans('plugins/hotel::ical.export_step2_title') }}</h4>
                                        <p>{{ trans('plugins/hotel::ical.export_step2_desc') }}</p>
                                    </div>
                                </div>

                                <div class="step">
                                    <div class="step-number">
                                        <x-core::badge label="3" color="primary" />
                                    </div>
                                    <div class="step-content">
                                        <h4>{{ trans('plugins/hotel::ical.export_step3_title') }}</h4>
                                        <p>{{ trans('plugins/hotel::ical.export_step3_desc') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="section">
                            <h3>
                                <x-core::icon name="ti ti-download" class="me-1" />
                                {{ trans('plugins/hotel::ical.import_bookings') }}
                            </h3>
                            <p>{{ trans('plugins/hotel::ical.import_bookings_desc') }}</p>

                            <div class="steps">
                                <div class="step">
                                    <div class="step-number">
                                        <x-core::badge label="1" color="primary" />
                                    </div>
                                    <div class="step-content">
                                        <h4>{{ trans('plugins/hotel::ical.import_step1_title') }}</h4>
                                        <p>{{ trans('plugins/hotel::ical.import_step1_desc') }}</p>
                                    </div>
                                </div>

                                <div class="step">
                                    <div class="step-number">
                                        <x-core::badge label="2" color="primary" />
                                    </div>
                                    <div class="step-content">
                                        <h4>{{ trans('plugins/hotel::ical.import_step2_title') }}</h4>
                                        <p>{{ trans('plugins/hotel::ical.import_step2_desc') }}</p>
                                    </div>
                                </div>

                                <div class="step">
                                    <div class="step-number">
                                        <x-core::badge label="3" color="primary" />
                                    </div>
                                    <div class="step-content">
                                        <h4>{{ trans('plugins/hotel::ical.import_step3_title') }}</h4>
                                        <p>{{ trans('plugins/hotel::ical.import_step3_desc') }}</p>
                                    </div>
                                </div>

                                <div class="step">
                                    <div class="step-number">
                                        <x-core::badge label="4" color="primary" />
                                    </div>
                                    <div class="step-content">
                                        <h4>{{ trans('plugins/hotel::ical.import_step4_title') }}</h4>
                                        <p>{{ trans('plugins/hotel::ical.import_step4_desc') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="section">
                            <h3>
                                <x-core::icon name="ti ti-history" class="me-1" />
                                {{ trans('plugins/hotel::ical.sync_logs') }}
                            </h3>
                            <p>{{ trans('plugins/hotel::ical.sync_logs_desc') }}</p>
                        </div>

                        <div class="section">
                            <h3>
                                <x-core::icon name="ti ti-tool" class="me-1" />
                                {{ trans('plugins/hotel::ical.troubleshooting') }}
                            </h3>
                            <p>{{ trans('plugins/hotel::ical.troubleshooting_desc') }}</p>

                            <div class="faq">
                                <div class="faq-item">
                                    <h4>
                                        <x-core::icon name="ti ti-calendar-off" class="me-1" />
                                        {{ trans('plugins/hotel::ical.faq1_title') }}
                                    </h4>
                                    <p>{{ trans('plugins/hotel::ical.faq1_desc') }}</p>
                                </div>

                                <div class="faq-item">
                                    <h4>
                                        <x-core::icon name="ti ti-alert-triangle" class="me-1" />
                                        {{ trans('plugins/hotel::ical.faq2_title') }}
                                    </h4>
                                    <p>{{ trans('plugins/hotel::ical.faq2_desc') }}</p>
                                </div>

                                <div class="faq-item">
                                    <h4>
                                        <x-core::icon name="ti ti-clock" class="me-1" />
                                        {{ trans('plugins/hotel::ical.faq3_title') }}
                                    </h4>
                                    <p>{{ trans('plugins/hotel::ical.faq3_desc') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('header')
    <style>
        .documentation {
            padding: 20px;
        }

        .section {
            margin-bottom: 30px;
        }

        .steps {
            margin-left: 20px;
        }

        .step {
            display: flex;
            margin-bottom: 20px;
        }

        .step-number {
            margin-right: 15px;
            flex-shrink: 0;
        }

        .step-content {
            flex: 1;
        }

        .faq-item {
            margin-bottom: 20px;
        }
    </style>
@endpush

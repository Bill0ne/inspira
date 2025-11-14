@php
    use Botble\Hotel\Services\CustomerCardService;

    $service = app(CustomerCardService::class);
    $selectedTemplateId = (int) old('template_id');
    $selectedTemplate = ($templates ?? collect())->firstWhere('id', $selectedTemplateId);
@endphp

<div class="row">
    <div class="col-lg-8">
        <x-core::card>
            <x-core::card.body>
                <div class="mb-4">
                    <label class="form-label fw-semibold" for="template_id">
                        {{ trans('plugins/hotel::customer-card.form.assignment.template') }}
                    </label>
                    <select
                        class="form-select"
                        name="template_id"
                        id="template_id"
                        data-bb-customer-card="template-select"
                        required
                    >
                        <option value="">
                            {{ trans('plugins/hotel::customer-card.form.assignment.template_placeholder') }}
                        </option>
                        @foreach ($templates as $template)
                            <option
                                value="{{ $template->getKey() }}"
                                data-name="{{ e($template->name) }}"
                                data-type="{{ e($template->type?->label() ?? '—') }}"
                                data-base-price="{{ $template->base_price }}"
                                data-discount="{{ $template->discount_percent }}"
                                data-total-units="{{ $template->units_total }}"
                                data-total-price="{{ $service->calculatePurchasePrice($template) }}"
                                data-valid-until="{{ optional($template->valid_until)->format(BaseHelper::getDateFormat()) }}"
                                @selected($template->getKey() === $selectedTemplateId)
                            >
                                {{ $template->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div
                    class="alert alert-secondary"
                    data-bb-customer-card="template-placeholder"
                    @if($selectedTemplate) style="display: none;" @endif
                >
                    {{ trans('plugins/hotel::customer-card.form.assignment.template_hint') }}
                </div>

                <div
                    class="bg-light border rounded-3 p-4"
                    data-bb-customer-card="template-summary"
                    @if(! $selectedTemplate) style="display: none;" @endif
                >
                    <div class="d-flex flex-column flex-md-row justify-content-between">
                        <div>
                            <h5 class="fw-semibold mb-1" data-bb-customer-card="template-name">
                                {{ $selectedTemplate?->name ?? '' }}
                            </h5>
                            <div class="text-muted" data-bb-customer-card="template-type">
                                {{ $selectedTemplate?->type?->label() ?? '' }}
                            </div>
                        </div>
                        <div class="text-md-end mt-3 mt-md-0">
                            <div class="fw-semibold" data-bb-customer-card="template-total-price">
                                @if ($selectedTemplate)
                                    {{ format_price($service->calculatePurchasePrice($selectedTemplate)) }}
                                @endif
                            </div>
                            <div class="text-muted small" data-bb-customer-card="template-base-price">
                                @if ($selectedTemplate)
                                    {{ format_price($selectedTemplate->base_price) }} × {{ number_format((float) $selectedTemplate->units_total, 0) }}
                                @endif
                            </div>
                        </div>
                    </div>

                    <dl class="row mb-0 mt-4">
                        <dt class="col-sm-5">
                            {{ trans('plugins/hotel::customer-card.table.units_remaining') }}
                        </dt>
                        <dd class="col-sm-7" data-bb-customer-card="template-units">
                            @if ($selectedTemplate)
                                {{ number_format((float) $selectedTemplate->units_total, 0) }}
                            @endif
                        </dd>

                        <dt class="col-sm-5">
                            {{ trans('plugins/hotel::customer-card.table.discount') }}
                        </dt>
                        <dd class="col-sm-7" data-bb-customer-card="template-discount">
                            @if ($selectedTemplate)
                                {{ number_format((float) $selectedTemplate->discount_percent, 2) }}%
                            @endif
                        </dd>

                        <dt class="col-sm-5">
                            {{ trans('plugins/hotel::customer-card.form.fields.valid_until') }}
                        </dt>
                        <dd class="col-sm-7" data-bb-customer-card="template-valid-until">
                            @if ($selectedTemplate)
                                {{ optional($selectedTemplate->valid_until)->format(BaseHelper::getDateFormat()) ?? '—' }}
                            @endif
                        </dd>
                    </dl>
                </div>
            </x-core::card.body>
        </x-core::card>
    </div>

    <div class="col-lg-4">
        <x-core::card class="mb-3">
            <x-core::card.body>
                <x-core::form.date-picker
                    :label="trans('plugins/hotel::customer-card.form.fields.valid_until')"
                    name="valid_until"
                    :value="old('valid_until', optional($selectedTemplate?->valid_until)->format(BaseHelper::getDateFormat()))"
                />

                <x-core::form.select
                    :label="trans('plugins/hotel::customer-card.form.fields.assigned_to')"
                    name="assigned_to"
                    :options="$customers"
                    :value="old('assigned_to')"
                    required
                />

                <x-core::form.checkbox
                    :label="trans('plugins/hotel::customer-card.form.assignment.is_active')"
                    name="is_active"
                    value="1"
                    :checked="old('is_active', true)"
                />
            </x-core::card.body>
        </x-core::card>

        <x-core::card>
            <x-core::card.body>
                <x-core::button type="submit" color="primary" class="w-100">
                    {{ trans('plugins/hotel::customer-card.save_button') }}
                </x-core::button>
            </x-core::card.body>
        </x-core::card>
    </div>
</div>

@include('plugins/hotel::customer-cards.partials.scripts', compact('jsValidator'))

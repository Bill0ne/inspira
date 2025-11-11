<div class="row">
    <div class="col-lg-8">
        <x-core::card>
            <x-core::card.body>
                <x-core::form.text-input
                    :label="trans('plugins/hotel::customer-card.form.fields.name')"
                    name="name"
                    :value="old('name', $card->name)"
                    required
                />

                <div class="row">
                    <div class="col-md-6">
                        <x-core::form.select
                            :label="trans('plugins/hotel::customer-card.form.fields.type')"
                            name="type"
                            :options="$types"
                            :value="old('type', $card->type?->getValue())"
                            data-bb-customer-card-select="type"
                        />
                    </div>
                    <div class="col-md-6">
                        <x-core::form.text-input
                            :label="trans('plugins/hotel::customer-card.form.fields.base_price')"
                            type="number"
                            name="base_price"
                            step="0.01"
                            min="0"
                            :value="old('base_price', $card->base_price)"
                            data-bb-customer-card-input="base-price"
                        />
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <x-core::form.text-input
                            :label="trans('plugins/hotel::customer-card.form.fields.discount_percent')"
                            type="number"
                            name="discount_percent"
                            step="0.01"
                            min="0"
                            max="100"
                            :value="old('discount_percent', $card->discount_percent)"
                            data-bb-customer-card-input="discount"
                        />
                    </div>
                    <div class="col-md-6">
                        <x-core::form.text-input
                            :label="trans('plugins/hotel::customer-card.form.fields.units_total')"
                            type="number"
                            name="units_total"
                            min="1"
                            :value="old('units_total', $card->units_total)"
                            data-bb-customer-card-input="units-total"
                        />
                    </div>
                </div>

                <div class="alert alert-info d-flex align-items-center justify-content-between" data-bb-customer-card="summary">
                    <div>
                        <strong>{{ trans('plugins/hotel::customer-card.form.summary.title') }}:</strong>
                        <span data-bb-customer-card="summary-text">
                            {{ trans('plugins/hotel::customer-card.form.summary.placeholder') }}
                        </span>
                    </div>
                    <span class="badge badge-pill badge-primary" data-bb-customer-card="summary-total"></span>
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
                    :value="old('valid_until', optional($card->valid_until)->format(BaseHelper::getDateFormat()))"
                />

                <x-core::form.select
                    :label="trans('plugins/hotel::customer-card.form.fields.assigned_to')"
                    name="assigned_to"
                    :options="['' => trans('plugins/hotel::customer-card.form.fields.not_assigned')] + $customers"
                    :value="old('assigned_to', $card->assigned_to)"
                />

                <x-core::form.checkbox
                    :label="trans('plugins/hotel::customer-card.form.fields.is_active')"
                    name="is_active"
                    value="1"
                    :checked="old('is_active', $card->is_active)"
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

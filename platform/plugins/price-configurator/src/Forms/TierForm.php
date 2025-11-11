<?php

namespace Botble\PriceConfigurator\Forms;

use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\OnOffField;
use Botble\Base\Forms\Fields\DatetimeField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\FormAbstract;
use Botble\Base\Forms\FieldOptions\NameFieldOption;
use Botble\Base\Forms\FieldOptions\NumberFieldOption;
use Botble\Base\Forms\FieldOptions\TextareaFieldOption;
use Botble\Base\Forms\FieldOptions\OnOffFieldOption;
use Botble\Base\Forms\FieldOptions\DatePickerFieldOption;
use Botble\Base\Forms\FieldOptions\StatusFieldOption;
use Botble\PriceConfigurator\Enums\PriceConfiguratorStatusEnum;
use Botble\PriceConfigurator\Http\Requests\TierRequest;
use Botble\PriceConfigurator\Models\Tier;

class TierForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->model(Tier::class)
            ->setValidatorClass(TierRequest::class)
            ->add('name',
                TextField::class,
                NameFieldOption::make()
                    ->label(trans('plugins/price-configurator::price-configurator.forms.tier_name'))
                    ->required()
            )
            ->add('priority',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/price-configurator::price-configurator.forms.priority'))
                    ->defaultValue(100)
            )
            ->add('is_exclusive',
                OnOffField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/price-configurator::price-configurator.forms.is_exclusive'))
                    ->defaultValue(false)
            )
            ->add('starts_at',
                DatetimeField::class,
                DatePickerFieldOption::make()
                    ->label(trans('plugins/price-configurator::price-configurator.forms.starts_at'))
            )
            ->add('ends_at',
                DatetimeField::class,
                DatePickerFieldOption::make()
                    ->label(trans('plugins/price-configurator::price-configurator.forms.ends_at'))
            )
            ->add('notes',
                TextareaField::class,
                TextareaFieldOption::make()
                    ->label(trans('plugins/price-configurator::price-configurator.forms.notes'))
            )
            ->add('status',
                SelectField::class,
                StatusFieldOption::make()
                    ->choices(PriceConfiguratorStatusEnum::labels())
            )
            ->setBreakFieldPoint('status');
    }
}

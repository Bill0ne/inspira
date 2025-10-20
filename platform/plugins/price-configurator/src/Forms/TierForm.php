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
                    ->label(__('Tier Name'))
                    ->required()
            )
            ->add('priority',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(__('Priority'))
                    ->defaultValue(100)
            )
            ->add('is_exclusive',
                OnOffField::class,
                OnOffFieldOption::make()
                    ->label(__('Is Exclusive'))
                    ->defaultValue(false)
            )
            ->add('starts_at',
                DatetimeField::class,
                DatePickerFieldOption::make()
                    ->label(__('Starts At'))
            )
            ->add('ends_at',
                DatetimeField::class,
                DatePickerFieldOption::make()
                    ->label(__('Ends At'))
            )
            ->add('notes',
                TextareaField::class,
                TextareaFieldOption::make()
                    ->label(__('Notes'))
            )
            ->add('status',
                SelectField::class,
                StatusFieldOption::make()
                    ->choices(PriceConfiguratorStatusEnum::labels())
            )
            ->setBreakFieldPoint('status');
    }
}

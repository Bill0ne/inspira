<?php

namespace Botble\PriceConfigurator\Forms;

use Botble\PriceConfigurator\Enums\CalculationTypeEnum;
use Botble\PriceConfigurator\Enums\ConditionTypeEnum;
use Botble\PriceConfigurator\Enums\PriceConfiguratorStatusEnum;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\FormAbstract;
use Botble\Base\Forms\FieldOptions\NameFieldOption;
use Botble\Base\Forms\FieldOptions\NumberFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\StatusFieldOption;
use Botble\PriceConfigurator\Http\Requests\QuantityDiscountRequest;
use Botble\PriceConfigurator\Models\QuantityDiscount;

class QuantityDiscountForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->model(QuantityDiscount::class)
            ->setValidatorClass(QuantityDiscountRequest::class)
            ->add('title',
                TextField::class,
                NameFieldOption::make()
                    ->label(__('Title'))
                    ->required()
            )
            ->add('condition_type',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(__('Condition Type'))
                    ->choices(ConditionTypeEnum::labels())
                    ->required()
            )
            ->add('range_min',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(__('Range Min'))
            )
            ->add('range_max',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(__('Range Max'))
            )
            ->add('discount_type', SelectField::class,
                SelectFieldOption::make()
                    ->label(__('Discount Type'))
                    ->choices(CalculationTypeEnum::labels())
                    ->required()
            )
            ->add('discount_value',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(__('Discount Value'))
                    ->required()
            )
            ->add('priority',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(__('Priority'))
                    ->defaultValue(0)
            )
            ->add('status',
                SelectField::class,
                StatusFieldOption::make()
                    ->choices(PriceConfiguratorStatusEnum::labels())
            )
            ->setBreakFieldPoint('status');
    }
}

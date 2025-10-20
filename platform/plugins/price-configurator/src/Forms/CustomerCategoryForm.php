<?php

namespace Botble\PriceConfigurator\Forms;

use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\FormAbstract;
use Botble\Base\Forms\FieldOptions\NameFieldOption;
use Botble\Base\Forms\FieldOptions\TextareaFieldOption;
use Botble\Base\Forms\FieldOptions\StatusFieldOption;
use Botble\PriceConfigurator\Http\Requests\CustomerCategoryRequest;
use Botble\PriceConfigurator\Models\CustomerCategory;
use Botble\PriceConfigurator\Enums\PriceConfiguratorStatusEnum;

class CustomerCategoryForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->model(CustomerCategory::class)
            ->setValidatorClass(CustomerCategoryRequest::class)
            ->add('code',
                TextField::class,
                NameFieldOption::make()
                    ->label(__('Code'))
                    ->required()
            )
            ->add('label',
                TextField::class,
                NameFieldOption::make()
                    ->label(__('Label'))
                    ->required()
            )
            ->add('description',
                TextareaField::class,
                TextareaFieldOption::make()
                    ->label(__('Description'))
            )
            ->add('status',
                SelectField::class,
                StatusFieldOption::make()
                    ->choices(PriceConfiguratorStatusEnum::labels())
            )
            ->setBreakFieldPoint('status');
    }
}

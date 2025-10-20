<?php

namespace Botble\PriceConfigurator\Forms;

use Botble\Base\Facades\Assets;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\FormAbstract;
use Botble\Base\Forms\FieldOptions\NumberFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\StatusFieldOption;
use Botble\PriceConfigurator\Enums\TargetTypeEnum;
use Botble\PriceConfigurator\Http\Requests\RuleRequest;
use Botble\PriceConfigurator\Models\Rule;
use Botble\PriceConfigurator\Models\Tier;
use Botble\PriceConfigurator\Models\CustomerCategory;
use Botble\PriceConfigurator\Enums\PriceConfiguratorStatusEnum;
use Botble\PriceConfigurator\Enums\ScopeEnum;
use Botble\PriceConfigurator\Enums\CalculationTypeEnum;
use Botble\PriceConfigurator\Enums\RoundingModeEnum;
use Botble\PriceConfigurator\Enums\RuleDirectionEnum;

class RuleForm extends FormAbstract
{
    public function setup(): void
    {
        Assets::addScriptsDirectly(['vendor/core/plugins/price-configurator/js/script.js']);

        $rule = $this->getModel();

        $targetIds = [];

        if (is_object($rule)) {
            $targetIds = is_string($rule->target_ids)
                ? json_decode($rule->target_ids, true)
                : ($rule->target_ids ?? []);
        }

        $this
            ->model(Rule::class)
            ->setValidatorClass(RuleRequest::class)
            ->add('price_tier_id',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(__('Price Tier'))
                    ->emptyValue(__('Select'))
                    ->choices(Tier::query()->where('status', PriceConfiguratorStatusEnum::ACTIVE)->pluck('name', 'id')->all())
                    ->searchable()
                    ->required()
            )
            ->add('customer_category_id',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(__('Customer Category'))
                    ->emptyValue(__('Select'))
                    ->choices(
                        CustomerCategory::query()
                            ->where('status', PriceConfiguratorStatusEnum::ACTIVE)
                            ->get()
                            ->mapWithKeys(function ($category) {
                                return [$category->id => "{$category->code} - {$category->label}"];
                            })
                            ->all()
                    )
                    ->searchable()
                    ->required()
            )
            ->add('adjustment_direction',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(__('Direction'))
                    ->emptyValue(__('Select'))
                    ->choices(RuleDirectionEnum::labels())
                    ->required()
            )
            ->add('scope',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(__('Scope'))
                    ->emptyValue(__('Select'))
                    ->choices(ScopeEnum::labels())
                    ->required()
                    ->attributes([
                        'data-toggle' => 'toggle-field',
                        'data-target' => '.target-type-wrapper, .target-ids-wrapper',
                    ])
            )
            ->add('target_type',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(__('Target Type'))
                    ->choices(TargetTypeEnum::labels())
                    ->emptyValue(__('Select'))
                    ->required()
                    ->selected($rule->target_type ?? '')
                    ->wrapperAttributes([
                        'class' => 'target-type-wrapper'
                    ])
                    ->attributes([
                        'data-url' => route('rule.list')
                    ])
            )
            ->add('target_ids',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(__('Applicable Categories / Products'))
                    ->multiple(true)
                    ->searchable()
                    ->selected($targetIds)
                    ->wrapperAttributes([
                        'class' => 'target-ids-wrapper mt-3 mb-3',
                        'data-selected' => implode(',', $targetIds)
                    ])
                    ->toArray()
            )
            ->add('calculation_type',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(__('Calculation Type'))
                    ->emptyValue(__('Select'))
                    ->choices(CalculationTypeEnum::labels())
                    ->required()
            )
            ->add('calculation_value',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(__('Calculation Value'))
                    ->defaultValue(0)
                    ->required()
            )
            ->add('rounding_mode',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(__('Rounding Mode'))
                    ->emptyValue(__('Select'))
                    ->choices(RoundingModeEnum::labels())
            )
            ->add('round_to',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(__('Round To'))
                    ->attributes([
                        'step' => '0.01',
                        'lang' => 'en'
                    ])
            )
            ->add('status',
                SelectField::class,
                StatusFieldOption::make()
                    ->choices(PriceConfiguratorStatusEnum::labels())
            )
            ->setBreakFieldPoint('status');
    }
}

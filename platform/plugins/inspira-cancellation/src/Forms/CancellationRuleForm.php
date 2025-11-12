<?php

namespace Botble\InspiraCancellation\Forms;

use Botble\Base\Forms\FieldOptions\NumberFieldOption;
use Botble\Base\Forms\FieldOptions\OnOffFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\TextareaFieldOption;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\OnOffField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\FormAbstract;
use Botble\InspiraCancellation\Http\Requests\CancellationRuleRequest;
use Botble\InspiraCancellation\Models\CancellationRule;

class CancellationRuleForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->model(CancellationRule::class)
            ->setValidatorClass(CancellationRuleRequest::class)
            ->add(
                'type',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(trans('plugins/inspira-cancellation::cancellation.rule.type'))
                    ->choices([
                        'course' => trans('plugins/courses::courses.course.name'),
                        'room' => trans('plugins/hotel::booking.room'),
                    ])
                    ->required()
            )
            ->add(
                'from_days',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/inspira-cancellation::cancellation.rule.from_days'))
                    ->min(0)
                    ->step(1)
            )
            ->add(
                'to_days',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/inspira-cancellation::cancellation.rule.to_days'))
                    ->min(0)
                    ->step(1)
            )
            ->add(
                'refund_percent',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/inspira-cancellation::cancellation.rule.refund_percent'))
                    ->required()
                    ->min(0)
                    ->max(100)
            )
            ->add(
                'description',
                TextareaField::class,
                TextareaFieldOption::make()
                    ->label(trans('plugins/inspira-cancellation::cancellation.rule.description'))
                    ->rows(4)
            )
            ->add(
                'order',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/inspira-cancellation::cancellation.rule.order'))
                    ->min(0)
                    ->step(1)
            )
            ->add(
                'active',
                OnOffField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/inspira-cancellation::cancellation.rule.active'))
            )
            ->setBreakFieldPoint('active');
    }
}

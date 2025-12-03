<?php

namespace Botble\Courses\Forms;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Facades\Assets;
use Botble\Base\Forms\FieldOptions\ContentFieldOption;
use Botble\Base\Forms\FieldOptions\DatePickerFieldOption;
use Botble\Base\Forms\FieldOptions\MediaImageFieldOption;
use Botble\Base\Forms\Fields\RepeaterField;
use Botble\Base\Forms\FieldOptions\RepeaterFieldOption;
use Botble\Base\Forms\FieldOptions\NumberFieldOption;
use Botble\Base\Forms\FieldOptions\NameFieldOption;
use Botble\Base\Forms\FieldOptions\OnOffFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\StatusFieldOption;
use Botble\Base\Forms\FieldOptions\TextareaFieldOption;
use Botble\Base\Forms\Fields\DatetimeField;
use Botble\Base\Forms\Fields\EditorField;
use Botble\Base\Forms\Fields\MediaImageField;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\OnOffField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\FormAbstract;
use Botble\Courses\Http\Requests\CourseRequest;
use Botble\Courses\Models\Course;
use Botble\Courses\Models\Instructor;
use Botble\Courses\Models\CourseCategory;
use Botble\Hotel\Models\Tax;
use Botble\Courses\Supports\CourseStatusManager;

class CourseForm extends FormAbstract
{
    public function setup(): void
    {
        /** @var Course $course */
        $course = $this->getModel();

        if ($course && $course->getKey()) {

            add_filter('base_action_form_actions_extra', function () use ($course) {
                return view('plugins/courses::extra-actions', compact('course'))->render();
            });
        }
        $taxes = [];
        $taxRates = [];
        $selectedTaxPercentage = 0;
        $currency = get_application_currency();

        $currencyFormat = [
            'symbol' => $currency->symbol ?? '',
            'is_prefix' => (bool) ($currency->is_prefix_symbol ?? true),
            'decimals' => (int) ($currency->decimals ?? 2),
            'decimal_separator' => ($decimalSeparator = setting('hotel_decimal_separator', '.')) === 'space' ? ' ' : $decimalSeparator,
            'thousand_separator' => ($thousandSeparator = setting('hotel_thousands_separator', ',')) === 'space' ? ' ' : $thousandSeparator,
            'add_space' => setting('hotel_add_space_between_price_and_currency', 0) == 1,
        ];

        if (is_plugin_active('hotel')) {
            $taxCollection = Tax::query()->get(['id', 'title', 'percentage']);

            $taxes = $taxCollection->pluck('title', 'id')->all();
            $taxRates = $taxCollection->mapWithKeys(fn (Tax $tax) => [$tax->getKey() => (float) $tax->percentage])->all();
            $selectedTaxPercentage = (float) ($course?->tax?->percentage ?? $taxCollection->first()?->percentage ?? 0);
        }

        $basePrice = (float) ($course?->price ?? 0);
        $grossPreview = course_truncate_price($basePrice * (1 + $selectedTaxPercentage / 100));

        Assets::addScriptsDirectly(['vendor/core/plugins/courses/js/script.js']);

        $manualSessionsData = [];

if ($course && $course->getKey()) {
    $manualSessions = $course->sessions()
        ->where('is_manual', true)
        ->orderBy('start_date')
        ->get(['id', 'start_date', 'end_date']);

    foreach ($manualSessions as $session) {
        $manualSessionsData[] = [
            [
                'key' => 'id',
                'value' => $session->id,
            ],
            [
                'key' => 'start',
                'value' => $session->start_date
                    ? \Carbon\Carbon::parse($session->start_date)->format('Y-m-d\TH:i')
                    : null,
            ],
            [
                'key' => 'end',
                'value' => $session->end_date
                    ? \Carbon\Carbon::parse($session->end_date)->format('Y-m-d\TH:i')
                    : null,
            ],
        ];
    }
}


        $this
            ->model(Course::class)
            ->setValidatorClass(CourseRequest::class)
            ->add('row', 'html', [
                'html' => '<div class="row">'
            ])
            ->add('category_id', SelectField::class,
                SelectFieldOption::make()
                    ->label(trans('plugins/courses::courses.course-category.category'))
                    ->choices(
                        CourseCategory::query()
                            ->where('status', BaseStatusEnum::PUBLISHED)
                            ->pluck('name', 'id')
                            ->all()
                    )
                    ->searchable()
                    ->wrapperAttributes(['class' => 'form-group col-md-6'])
                    ->required()
            )
            ->add('instructor_id', SelectField::class,
                SelectFieldOption::make()
                    ->label(trans('plugins/courses::courses.instructor.instructor'))
                    ->choices(
                        Instructor::query()
                            ->where('status', BaseStatusEnum::PUBLISHED)
                            ->pluck('name', 'id')
                            ->all()
                    )
                    ->searchable()
                    ->wrapperAttributes(['class' => 'form-group col-md-6'])
                    ->required()
            )
            ->when(is_plugin_active('hotel'), function ($form) {
                $form->add('room_id', SelectField::class,
                    SelectFieldOption::make()
                        ->label('Room')
                        ->emptyValue(trans('Zimmer auswählen'))
                        ->choices(
                            \Botble\Hotel\Models\Room::query()
                                ->where('status', BaseStatusEnum::PUBLISHED)
                                ->pluck('name', 'id')
                                ->all()
                        )
                        ->searchable()
                        ->wrapperAttributes(['class' => 'form-group col-md-6'])

                );
            })
            ->add('name', TextField::class, NameFieldOption::make()->required()->wrapperAttributes(['class' => 'form-group col-md-6']))
            ->add('description', EditorField::class,
                ContentFieldOption::make()
                    ->label(trans('core/base::forms.description'))
                    ->rows(4)
                    ->placeholder('Enter course description')
            )
            ->add(
                'unlimited_seats',
                OnOffField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/courses::courses.course.unlimited_seats'))
                    ->checked($this->getModel() && $this->getModel()->unlimited_seats)
                    ->attributes([
                        'class' => 'form-group',
                        'data-toggle' => 'toggle-field',
                        'data-target' => '.seats-wrapper',
                    ])
            )
            ->add(
                'number_of_seats',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/courses::courses.course.number_of_seats'))
                    ->wrapperAttributes(['class' => 'form-group seats-wrapper'])
                    ->attributes([
                        'class' => 'form-control',
                    ])
            )
            ->add('price', NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/courses::courses.course.price'))
                    ->wrapperAttributes(['class' => 'form-group col-md-6'])
                    ->attributes([
                        'step' => '0.0001',
                        'lang' => 'en',
                    ])
                    ->helperText(view('plugins/courses::partials.price-helper', [
                        'grossPreview' => course_format_price($grossPreview),
                        'selectedTaxPercentage' => $selectedTaxPercentage,
                        'taxRates' => $taxRates,
                        'currencyFormat' => $currencyFormat,
                    ])->render())
                    ->required()
            )
            ->when(is_plugin_active('hotel'), function ($form) {
                $form->add('accept_customer_card', OnOffField::class,
                    OnOffFieldOption::make()
                        ->label(trans('plugins/hotel::customer-card.form.fields.accept_customer_card'))
                        ->checked((bool) ($form->getModel()?->accept_customer_card ?? true))
                        ->defaultValue(true)
                        ->value($form->getModel()?->accept_customer_card ?? true)
                        ->wrapperAttributes(['class' => 'form-group col-md-6'])
                );
            })
            ->add('duration', TextField::class,
                NameFieldOption::make()
                    ->label(trans('plugins/courses::courses.course.duration'))
                    ->wrapperAttributes(['class' => 'form-group col-md-6'])
                    ->placeholder('e.g. 10 weeks, 40 hours')
            )
            ->add('manual_info_alert', 'html', [
                'html' => '<div class="col-lg-12 mt-1 mb-3"><h4 class="m-0">Manuelle Sitzungen</h4></div>',
            ])
            ->add('start_date', DatetimeField::class,
                DatePickerFieldOption::make()
                    ->label(trans('plugins/courses::courses.course.start_date'))
                    ->wrapperAttributes(['class' => 'form-group col-md-6'])
                    ->required()
            )
            ->add('end_date', DatetimeField::class,
                DatePickerFieldOption::make()
                    ->label(trans('plugins/courses::courses.course.end_date'))
                    ->wrapperAttributes(['class' => 'form-group col-md-6'])
            )
            ->add('manual_sessions', RepeaterField::class,
                RepeaterFieldOption::make()
                    ->label(false)
                    ->fields([
                        [
                            'type' => 'hidden',
                            'label' => false,
                            'attributes' => [
                                'name' => 'id',
                                'value' => null,
                            ],
                        ],
                        [
                            'type' => 'datetimeLocal',
                            'label' => trans('plugins/courses::courses.course.start_date'),
                            'attributes' => [
                                'name' => 'start',
                                'value' => null,
                                'options' => [
                                    'class' => 'form-control',
                                ],
                            ],
                            'wrapperAttributes' => [
                                'class' => 'col-md-6',
                            ],
                        ],
                        [
                            'type' => 'datetimeLocal',
                            'label' => trans('plugins/courses::courses.course.end_date'),
                            'attributes' => [
                                'name' => 'end',
                                'value' => null,
                                'options' => [
                                    'class' => 'form-control',
                                ],
                            ],
                            'wrapperAttributes' => [
                                'class' => 'col-md-6',
                            ],
                        ],
                    ])
                    ->value($manualSessionsData)
            )
            ->add('recurring_info_alert', 'html', [
                'html' => '<div class="col-lg-12 mt-1"><div role="alert" class="alert alert-primary bg-primary text-white"><h4 class="m-0">Recurring Information</h4></div></div>',
            ])
            ->add('is_recurring', OnOffField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/courses::courses.course.is_recurring'))
                    ->attributes([
                        'data-toggle' => 'toggle-field',
                        'data-target' => '.recurring-wrapper',
                    ])
            )
            ->add('recurring_type', SelectField::class,
                SelectFieldOption::make()
                    ->label(trans('plugins/courses::courses.course.recurring_type'))
                    ->choices([
                        'daily' => __('Täglich'),
                        'weekly' => __('Wöchentlich'),
                        'monthly' => __('Monatlich'),
                    ])
                    ->helperText(__('Wie oft sollte dieser Kurs wiederholt werden?'))
                    ->wrapperAttributes(['class' => 'form-group recurring-wrapper col-md-4'])
            )
            ->add('recurring_interval', NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/courses::courses.course.recurring_interval'))
                    ->defaultValue(1)
                    ->helperText(__('Alle X Tage/Wochen/Monate'))
                    ->wrapperAttributes(['class' => 'form-group recurring-wrapper col-md-4'])
            )
            ->add('recurring_until', DatetimeField::class,
                DatePickerFieldOption::make()
                    ->label(trans('plugins/courses::courses.course.recurring_until'))
                    ->helperText(__('Bis zu welchem ​​Datum soll es wiederholt werden?'))
                    ->wrapperAttributes(['class' => 'form-group recurring-wrapper col-md-4'])
            )
            ->add('rowClose', 'html', [
                'html' => '</div>'
            ])
            ->add('thumbnail', MediaImageField::class, MediaImageFieldOption::make()->label(trans('core/acl::users.avatar')))
            ->add(
                'is_featured',
                OnOffField::class,
                OnOffFieldOption::make()
                    ->label(trans('core/base::forms.is_featured'))
                    ->defaultValue(false)
                    ->toArray()
            )
            ->add('status', SelectField::class, StatusFieldOption::make()->choices(CourseStatusManager::labels()))
            ->add('tax_id', 'customSelect', [
                'label' => trans('plugins/hotel::room.form.tax'),
                'required' => true,
                'wrapper' => [
                    'class' => $this->formHelper->getConfig('defaults.wrapper_class') . ' col-md-4',
                ],
                'attr' => [
                    'class' => 'form-control select-full',
                ],
                'choices' => $taxes,
            ])
            ->setBreakFieldPoint('thumbnail');
    }
}

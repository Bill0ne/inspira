<?php

namespace Botble\Courses\Forms;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Facades\Assets;
use Botble\Base\Forms\FieldOptions\ContentFieldOption;
use Botble\Base\Forms\FieldOptions\DatePickerFieldOption;
use Botble\Base\Forms\FieldOptions\MediaImageFieldOption;
use Botble\Base\Forms\Fields\CheckboxField;
use Botble\Base\Forms\FieldOptions\CheckboxFieldOption;
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

        Assets::addScriptsDirectly(['vendor/core/plugins/courses/js/script.js']);

        $this
            ->model(Course::class)
            ->setValidatorClass(CourseRequest::class)
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
                    ->required()
            )
            ->add('name', TextField::class, NameFieldOption::make()->required())
            ->add('description', EditorField::class,
                ContentFieldOption::make()
                    ->label(trans('core/base::forms.description'))
                    ->rows(4)
                    ->placeholder('Enter course description')
            )
            ->add('price', NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/courses::courses.course.price'))
                    ->required()
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
            ->add('duration', TextField::class,
                NameFieldOption::make()
                    ->label(trans('plugins/courses::courses.course.duration'))
                    ->placeholder('e.g. 10 weeks, 40 hours')
            )
            ->add('start_date', DatetimeField::class,
                DatePickerFieldOption::make()
                    ->label(trans('plugins/courses::courses.course.start_date'))
                    ->required()
            )
            ->add('end_date', DatetimeField::class,
                DatePickerFieldOption::make()
                    ->label(trans('plugins/courses::courses.course.end_date'))
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
                        'daily' => __('Daily'),
                        'weekly' => __('Weekly'),
                        'monthly' => __('Monthly'),
                    ])
                    ->helperText(__('How often should this course repeat?'))
                    ->wrapperAttributes(['class' => 'form-group recurring-wrapper'])
            )
            ->add('recurring_interval', NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/courses::courses.course.recurring_interval'))
                    ->defaultValue(1)
                    ->helperText(__('Every X days/weeks/months'))
                    ->wrapperAttributes(['class' => 'form-group recurring-wrapper'])
            )
            ->add('recurring_until', DatetimeField::class,
                DatePickerFieldOption::make()
                    ->label(trans('plugins/courses::courses.course.recurring_until'))
                    ->helperText(__('Until what date should it repeat?'))
                    ->wrapperAttributes(['class' => 'form-group recurring-wrapper'])
            )
            ->add('thumbnail', MediaImageField::class, MediaImageFieldOption::make()->label(trans('core/acl::users.avatar')))
            ->add(
                'is_featured',
                OnOffField::class,
                OnOffFieldOption::make()
                    ->label(trans('core/base::forms.is_featured'))
                    ->defaultValue(false)
                    ->toArray()
            )
            ->add('status', SelectField::class, StatusFieldOption::make())
            ->setBreakFieldPoint('thumbnail');
    }
}

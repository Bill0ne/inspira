<?php

namespace Botble\Courses\Forms;

use Botble\Base\Forms\FieldOptions\NameFieldOption;
use Botble\Base\Forms\FieldOptions\StatusFieldOption;
use Botble\Base\Forms\FieldOptions\TextareaFieldOption;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FormAbstract;
use Botble\Courses\Http\Requests\CourseCategoryRequest;
use Botble\Courses\Models\CourseCategory;

class CourseCategoryForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->model(CourseCategory::class)
            ->setValidatorClass(CourseCategoryRequest::class)
            ->add('name', TextField::class, NameFieldOption::make()->required())
            ->add('description', TextareaField::class, TextareaFieldOption::make()
                ->label(trans('core/base::forms.description'))
                ->rows(4)
                ->placeholder(trans('core/base::forms.description'))
            )
            ->add('status', SelectField::class, StatusFieldOption::make())
            ->setBreakFieldPoint('status');
    }
}

<?php

namespace Botble\Courses\Forms;

use Botble\Base\Forms\FieldOptions\ContentFieldOption;
use Botble\Base\Forms\FieldOptions\EmailFieldOption;
use Botble\Base\Forms\FieldOptions\NameFieldOption;
use Botble\Base\Forms\FieldOptions\StatusFieldOption;
use Botble\Base\Forms\FieldOptions\TextareaFieldOption;
use Botble\Base\Forms\FieldOptions\MediaImageFieldOption;
use Botble\Base\Forms\Fields\EditorField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\MediaImageField;
use Botble\Base\Forms\FormAbstract;
use Botble\Courses\Http\Requests\InstructorRequest;
use Botble\Courses\Models\Instructor;

class InstructorForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->model(Instructor::class)
            ->setValidatorClass(InstructorRequest::class)
            ->add('name', TextField::class, NameFieldOption::make()->required())
            ->add('email', TextField::class, EmailFieldOption::make())
            ->add('phone', TextField::class, NameFieldOption::make()->label(trans('plugins/courses::courses.instructor.phone'))->placeholder(trans('plugins/courses::courses.instructor.phone')))
            ->add('bio', EditorField::class, ContentFieldOption::make()->label(trans('plugins/courses::courses.instructor.bio'))->allowedShortcodes())
            ->add('status', SelectField::class, StatusFieldOption::make())
            ->add('photo', MediaImageField::class, MediaImageFieldOption::make()->label(trans('core/acl::users.avatar')))
            ->setBreakFieldPoint('status');
    }
}

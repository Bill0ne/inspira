<?php

namespace Botble\Courses\Http\Controllers;

use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Courses\Http\Requests\InstructorRequest;
use Botble\Courses\Models\Instructor;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Courses\Tables\InstructorTable;
use Botble\Courses\Forms\InstructorForm;

class InstructorController extends BaseController
{
    public function __construct()
    {
        $this
            ->breadcrumb()
            ->add(trans(trans('plugins/courses::courses.instructor.name')), route('instructor.index'));
    }

    public function index(InstructorTable $table)
    {
        $this->pageTitle(trans('plugins/courses::courses.instructor.name'));

        return $table->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/courses::courses.instructor.create'));

        return InstructorForm::create()->renderForm();
    }

    public function store(InstructorRequest $request)
    {
        $form = InstructorForm::create()->setRequest($request);

        $form->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('instructor.index'))
            ->setNextUrl(route('instructor.edit', $form->getModel()->getKey()))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(Instructor $instructor)
    {
        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $instructor->name]));

        return InstructorForm::createFromModel($instructor)->renderForm();
    }

    public function update(Instructor $instructor, InstructorRequest $request)
    {
        InstructorForm::createFromModel($instructor)
            ->setRequest($request)
            ->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('instructor.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Instructor $instructor)
    {
        return DeleteResourceAction::make($instructor);
    }
}

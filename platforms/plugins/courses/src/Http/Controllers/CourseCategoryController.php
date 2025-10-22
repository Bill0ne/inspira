<?php

namespace Botble\Courses\Http\Controllers;

use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Courses\Http\Requests\CourseCategoryRequest;
use Botble\Courses\Models\CourseCategory;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Courses\Tables\CourseCategoryTable;
use Botble\Courses\Forms\CourseCategoryForm;

class CourseCategoryController extends BaseController
{
    public function __construct()
    {
        $this
            ->breadcrumb()
            ->add(trans(trans('plugins/courses::courses.course-category.name')), route('course-category.index'));
    }

    public function index(CourseCategoryTable $table)
    {
        $this->pageTitle(trans('plugins/courses::courses.course-category.name'));

        return $table->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/courses::courses.course-category.create'));

        return CourseCategoryForm::create()->renderForm();
    }

    public function store(CourseCategoryRequest $request)
    {
        $form = CourseCategoryForm::create()->setRequest($request);

        $form->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('course-category.index'))
            ->setNextUrl(route('course-category.edit', $form->getModel()->getKey()))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(CourseCategory $courseCategory)
    {
        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $courseCategory->name]));

        return CourseCategoryForm::createFromModel($courseCategory)->renderForm();
    }

    public function update(CourseCategory $courseCategory, CourseCategoryRequest $request)
    {
        CourseCategoryForm::createFromModel($courseCategory)
            ->setRequest($request)
            ->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('course-category.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(CourseCategory $courseCategory)
    {
        return DeleteResourceAction::make($courseCategory);
    }
}

<?php

namespace Botble\PriceConfigurator\Http\Controllers;

use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\PriceConfigurator\Http\Requests\RuleRequest;
use Botble\PriceConfigurator\Models\Rule;
use Botble\Base\Http\Controllers\BaseController;
use Botble\PriceConfigurator\Tables\RuleTable;
use Botble\PriceConfigurator\Forms\RuleForm;
use Botble\PriceConfigurator\Enums\ScopeEnum;
use Botble\PriceConfigurator\Enums\TargetTypeEnum;
use Illuminate\Http\Request;

class RuleController extends BaseController
{
    public function __construct()
    {
        $this
            ->breadcrumb()
            ->add(trans(trans('plugins/price-configurator::price-configurator.rule.name')), route('rule.index'));
    }

    public function index(RuleTable $table)
    {
        $this->pageTitle(trans('plugins/price-configurator::price-configurator.rule.name'));

        return $table->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/price-configurator::price-configurator.customer-category.create'));

        return RuleForm::create()->renderForm();
    }

    public function store(RuleRequest $request)
    {
        $form = RuleForm::create()->setRequest($request);

        $form->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('rule.index'))
            ->setNextUrl(route('rule.edit', $form->getModel()->getKey()))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(Rule $rule)
    {
        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $rule->name]));

        return RuleForm::createFromModel($rule)->renderForm();
    }

    public function update(Rule $rule, RuleRequest $request)
    {
        RuleForm::createFromModel($rule)
            ->setRequest($request)
            ->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('rule.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Rule $rule)
    {
        return DeleteResourceAction::make($rule);
    }

    public function ajaxTargets(Request $request)
    {
        $scope = $request->input('scope');
        $type = $request->input('target_type');
        $results = [];

        if ($scope == ScopeEnum::BY_CATEGORY()) {
            if ($type == TargetTypeEnum::COURSE()) {
                $results = \Botble\Courses\Models\CourseCategory::query()->pluck('name','id');
            } else {
                $results = \Botble\Hotel\Models\RoomCategory::query()->pluck('name','id');
            }
        } elseif ($scope == ScopeEnum::SPECIFIC_PRODUCTS()) {
            if ($type == TargetTypeEnum::COURSE()) {
                $results = \Botble\Courses\Models\Course::query()->pluck('name','id');
            } else {
                $results = \Botble\Hotel\Models\Room::query()->pluck('name','id');
            }
        }

        return response()->json($results);
    }

}

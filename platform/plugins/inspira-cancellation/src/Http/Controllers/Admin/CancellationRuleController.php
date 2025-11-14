<?php

namespace Botble\InspiraCancellation\Http\Controllers\Admin;

use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Supports\Breadcrumb;
use Botble\InspiraCancellation\Forms\CancellationRuleForm;
use Botble\InspiraCancellation\Http\Requests\CancellationRuleRequest;
use Botble\InspiraCancellation\Models\CancellationRule;
use Botble\InspiraCancellation\Tables\CancellationRuleTable;
use Illuminate\Http\JsonResponse;

class CancellationRuleController extends BaseController
{
    protected function breadcrumb(): Breadcrumb
    {
        return parent::breadcrumb()
            ->add(
                trans('plugins/inspira-cancellation::cancellation.rule.list'),
                route('inspira-cancellation.rules.index')
            );
    }

    public function index(CancellationRuleTable $table)
    {
        $this->pageTitle(trans('plugins/inspira-cancellation::cancellation.rule.list'));

        if (request()->ajax() || request()->expectsJson()) {
            return $table->ajax();
        }

        return $table->renderTable();
    }

    public function getData(CancellationRuleTable $table): JsonResponse
    {
        return $table->ajax();
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/inspira-cancellation::cancellation.rule.create'));

        return CancellationRuleForm::create()->renderForm();
    }

    public function store(CancellationRuleRequest $request)
    {
        $form = CancellationRuleForm::create()->setRequest($request);
        $form->save();

        return $this
            ->httpResponse()
            ->setPreviousRoute('inspira-cancellation.rules.index')
            ->setNextRoute('inspira-cancellation.rules.edit', $form->getModel()->getKey())
            ->withCreatedSuccessMessage();
    }

    public function edit(CancellationRule $rule)
    {
        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $rule->getKey()]));

        return CancellationRuleForm::createFromModel($rule)->renderForm();
    }

    public function update(CancellationRule $rule, CancellationRuleRequest $request)
    {
        CancellationRuleForm::createFromModel($rule)->setRequest($request)->save();

        return $this
            ->httpResponse()
            ->setPreviousRoute('inspira-cancellation.rules.index')
            ->withUpdatedSuccessMessage();
    }

    public function destroy(CancellationRule $rule)
    {
        return DeleteResourceAction::make($rule);
    }
}

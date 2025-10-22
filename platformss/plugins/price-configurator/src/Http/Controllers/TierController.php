<?php

namespace Botble\PriceConfigurator\Http\Controllers;

use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\PriceConfigurator\Http\Requests\TierRequest;
use Botble\PriceConfigurator\Models\Tier;
use Botble\Base\Http\Controllers\BaseController;
use Botble\PriceConfigurator\Tables\TierTable;
use Botble\PriceConfigurator\Forms\TierForm;

class TierController extends BaseController
{
    public function __construct()
    {
        $this
            ->breadcrumb()
            ->add(trans(trans('plugins/price-configurator::price-configurator.tier.name')), route('tier.index'));
    }

    public function index(TierTable $table)
    {
        $this->pageTitle(trans('plugins/price-configurator::price-configurator.tier.name'));

        return $table->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/price-configurator::price-configurator.customer-category.create'));

        return TierForm::create()->renderForm();
    }

    public function store(TierRequest $request)
    {
        $form = TierForm::create()->setRequest($request);

        $form->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('tier.index'))
            ->setNextUrl(route('tier.edit', $form->getModel()->getKey()))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(Tier $tier)
    {
        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $tier->name]));

        return TierForm::createFromModel($tier)->renderForm();
    }

    public function update(Tier $tier, TierRequest $request)
    {
        TierForm::createFromModel($tier)
            ->setRequest($request)
            ->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('tier.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Tier $tier)
    {
        return DeleteResourceAction::make($tier);
    }
}

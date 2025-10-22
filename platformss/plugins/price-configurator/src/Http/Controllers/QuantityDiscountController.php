<?php

namespace Botble\PriceConfigurator\Http\Controllers;

use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\PriceConfigurator\Http\Requests\QuantityDiscountRequest;
use Botble\PriceConfigurator\Models\QuantityDiscount;
use Botble\Base\Http\Controllers\BaseController;
use Botble\PriceConfigurator\Tables\QuantityDiscountTable;
use Botble\PriceConfigurator\Forms\QuantityDiscountForm;

class QuantityDiscountController extends BaseController
{
    public function __construct()
    {
        $this
            ->breadcrumb()
            ->add(trans(trans('plugins/price-configurator::price-configurator.quantity-discount.name')), route('quantity-discount.index'));
    }

    public function index(QuantityDiscountTable $table)
    {
        $this->pageTitle(trans('plugins/price-configurator::price-configurator.quantity-discount.name'));

        return $table->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/price-configurator::price-configurator.customer-category.create'));

        return QuantityDiscountForm::create()->renderForm();
    }

    public function store(QuantityDiscountRequest $request)
    {
        $form = QuantityDiscountForm::create()->setRequest($request);

        $form->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('quantity-discount.index'))
            ->setNextUrl(route('quantity-discount.edit', $form->getModel()->getKey()))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(QuantityDiscount $quantityDiscount)
    {
        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $quantityDiscount->title]));

        return QuantityDiscountForm::createFromModel($quantityDiscount)->renderForm();
    }

    public function update(QuantityDiscount $quantityDiscount, QuantityDiscountRequest $request)
    {
        QuantityDiscountForm::createFromModel($quantityDiscount)
            ->setRequest($request)
            ->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('quantity-discount.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(QuantityDiscount $quantityDiscount)
    {
        return DeleteResourceAction::make($quantityDiscount);
    }
}

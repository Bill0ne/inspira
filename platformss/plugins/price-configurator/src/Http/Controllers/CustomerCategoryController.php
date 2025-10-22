<?php

namespace Botble\PriceConfigurator\Http\Controllers;

use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\PriceConfigurator\Http\Requests\CustomerCategoryRequest;
use Botble\PriceConfigurator\Models\CustomerCategory;
use Botble\Base\Http\Controllers\BaseController;
use Botble\PriceConfigurator\Tables\CustomerCategoryTable;
use Botble\PriceConfigurator\Forms\CustomerCategoryForm;

class CustomerCategoryController extends BaseController
{
    public function __construct()
    {
        $this
            ->breadcrumb()
            ->add(trans(trans('plugins/price-configurator::price-configurator.customer-category.name')), route('customer-category.index'));
    }

    public function index(CustomerCategoryTable $table)
    {
        $this->pageTitle(trans('plugins/price-configurator::price-configurator.customer-category.name'));

        return $table->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/price-configurator::price-configurator.customer-category.create'));

        return CustomerCategoryForm::create()->renderForm();
    }

    public function store(CustomerCategoryRequest $request)
    {
        $form = CustomerCategoryForm::create()->setRequest($request);

        $form->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('customer-category.index'))
            ->setNextUrl(route('customer-category.edit', $form->getModel()->getKey()))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(CustomerCategory $customerCategory)
    {
        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $customerCategory->label]));

        return CustomerCategoryForm::createFromModel($customerCategory)->renderForm();
    }

    public function update(CustomerCategory $customerCategory, CustomerCategoryRequest $request)
    {
        CustomerCategoryForm::createFromModel($customerCategory)
            ->setRequest($request)
            ->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('customer-category.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(CustomerCategory $customerCategory)
    {
        return DeleteResourceAction::make($customerCategory);
    }
}

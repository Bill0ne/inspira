<?php

namespace Botble\Hotel\Forms;

use Botble\Base\Forms\FieldOptions\StatusFieldOption;
use Botble\Base\Forms\Fields\MediaImageField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\FormAbstract;
use Botble\Hotel\Http\Requests\CustomerEditRequest;
use Botble\Hotel\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CustomerForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->setupModel(new Customer())
            // Eine robuste Request-Klasse für Create & Edit
            ->setValidatorClass(CustomerEditRequest::class)
            ->withCustomFields()
            ->add('first_name', 'text', [
                'label' => trans('plugins/hotel::customer.form.first_name'),
                'required' => true,
                'attr' => [
                    'placeholder' => trans('plugins/hotel::customer.form.first_name_placeholder'),
                    'data-counter' => 120,
                ],
            ])
            ->add('last_name', 'text', [
                'label' => trans('plugins/hotel::customer.form.last_name'),
                'required' => true,
                'attr' => [
                    'placeholder' => trans('plugins/hotel::customer.form.last_name_placeholder'),
                    'data-counter' => 120,
                ],
            ])
            ->add('email', 'text', [
                'label' => trans('plugins/hotel::customer.form.email'),
                'required' => true,
                'attr' => [
                    'placeholder' => trans('plugins/hotel::customer.form.email_placeholder'),
                    'data-counter' => 60,
                ],
            ])
            ->add('phone', 'text', [
                'label' => trans('plugins/hotel::customer.form.phone'),
                'attr' => [
                    'placeholder' => trans('plugins/hotel::customer.form.phone'),
                    'data-counter' => 20,
                ],
            ])
            ->add('is_change_password', 'onOff', [
                'label' => trans('plugins/hotel::customer.change_password'),
                'value' => 0,
                'attr' => [
                    'data-bb-toggle' => 'collapse',
                    'data-bb-target' => '#change-password',
                ],
                'wrapper' => [
                    'class' => $this->getModel()->id ? $this->formHelper->getConfig('defaults.wrapper_class') : 'd-none',
                ],
            ])
            ->add('openRow', 'html', [
                'html' => '<div id="change-password" class="row"' . ($this->getModel()->id ? ' style="display: none"' : null) . '>',
            ])
            ->add('password', 'password', [
                'label' => trans('plugins/hotel::customer.password'),
                'required' => true,
                'attr' => ['data-counter' => 60],
                'wrapper' => [
                    'class' => $this->formHelper->getConfig('defaults.wrapper_class') . ' col-md-6',
                ],
            ])
            ->add('password_confirmation', 'password', [
                'label' => trans('plugins/hotel::customer.password_confirmation'),
                'required' => true,
                'attr' => ['data-counter' => 60],
                'wrapper' => [
                    'class' => $this->formHelper->getConfig('defaults.wrapper_class') . ' col-md-6',
                ],
            ])
            ->add('closeRow', 'html', ['html' => '</div>']);

        // Kategorien aus Price Configurator
        $choices = [];
        if (Schema::hasTable('pc_customer_categories')) {
            $choices = DB::table('pc_customer_categories')
                ->where('status', 'active')
                ->orderBy('code')
                ->pluck('label', 'code')
                ->toArray();
        }
        if (! array_key_exists('STANDARD', $choices)) {
            $choices = ['STANDARD' => 'STANDARD'] + $choices;
        }

        $this->add('customer_category', 'customSelect', [
            'label'       => 'Kundenkategorie',
            'choices'     => $choices,
            'selected'    => old('customer_category', $this->getModel()->customer_category ?? 'STANDARD'),
            'empty_value' => null,
            'attr'        => ['data-placeholder' => 'Kategorie wählen.'],
            'help_block'  => ['text' => 'Wird vom Preiskonfigurator für Regeln genutzt.'],
        ]);

        $this
            ->add('status', SelectField::class, StatusFieldOption::make()->toArray())
            ->add('avatar', MediaImageField::class)
            ->setBreakFieldPoint('status');
    }
}

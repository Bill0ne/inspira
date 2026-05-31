<?php

namespace Botble\Community\Forms;

use Botble\Base\Forms\FieldOptions\ContentFieldOption;
use Botble\Base\Forms\FieldOptions\MediaImageFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\StatusFieldOption;
use Botble\Base\Forms\FieldOptions\TextareaFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\EditorField;
use Botble\Base\Forms\Fields\MediaImageField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FormAbstract;
use Botble\Community\Http\Requests\CommunityMemberRequest;
use Botble\Community\Models\CommunityMember;
use Botble\Hotel\Models\Customer;

class CommunityMemberForm extends FormAbstract
{
    public function setup(): void
    {
        $customers = Customer::query()
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get(['id', 'first_name', 'last_name', 'email'])
            ->mapWithKeys(fn (Customer $customer) => [
                $customer->getKey() => trim($customer->name . ' (' . $customer->email . ')'),
            ])
            ->toArray();

        $this
            ->model(CommunityMember::class)
            ->setValidatorClass(CommunityMemberRequest::class)
            ->add(
                'customer_id',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(trans('plugins/community::community.form.customer'))
                    ->choices($customers)
                    ->searchable()
                    ->helperText(trans('plugins/community::community.form.customer_helper'))
                    ->required()
            )
            ->add(
                'name',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/community::community.form.display_name'))
                    ->helperText(trans('plugins/community::community.form.display_name_helper'))
                    ->maxLength(255)
            )
            ->add(
                'short_description',
                TextareaField::class,
                TextareaFieldOption::make()
                    ->label(trans('plugins/community::community.form.short_description'))
                    ->helperText(trans('plugins/community::community.form.short_description_helper'))
                    ->rows(2)
                    ->maxLength(400)
            )
            ->add(
                'quote',
                TextareaField::class,
                TextareaFieldOption::make()
                    ->label(trans('plugins/community::community.form.quote'))
                    ->rows(3)
                    ->maxLength(1000)
            )
            ->add(
                'description',
                EditorField::class,
                ContentFieldOption::make()
                    ->label(trans('plugins/community::community.form.description'))
                    ->allowedShortcodes()
            )
            ->add('status', SelectField::class, StatusFieldOption::make())
            ->add(
                'photo',
                MediaImageField::class,
                MediaImageFieldOption::make()->label(trans('plugins/community::community.form.photo'))
            )
            ->setBreakFieldPoint('status');
    }
}

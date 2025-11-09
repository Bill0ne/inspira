<?php

namespace Botble\Hotel\Http\Controllers;

use Botble\ACL\Models\User;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Facades\Assets;
use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Facades\BaseHelper;
use Botble\Hotel\Enums\CustomerCardTypeEnum;
use Botble\Hotel\Http\Requests\CustomerCardRequest;
use Botble\Hotel\Models\CustomerCard;
use Botble\Hotel\Tables\CustomerCardTable;
use Botble\JsValidation\Facades\JsValidator;
use Carbon\Carbon;

class CustomerCardController extends BaseController
{
    public function __construct()
    {
        $this
            ->breadcrumb()
            ->add(trans('plugins/hotel::customer-card.name'), route('customer-cards.index'));
    }

    public function index(CustomerCardTable $table)
    {
        $this->pageTitle(trans('plugins/hotel::customer-card.name'));

        return $table->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/hotel::customer-card.form.create'));

        Assets::addScripts(['form-validation'])
            ->addScriptsDirectly('vendor/core/plugins/hotel/js/customer-card.js');

        $jsValidator = JsValidator::formRequest(CustomerCardRequest::class);
        $card = new CustomerCard();
        $users = User::query()
            ->get()
            ->mapWithKeys(function (User $user) {
                $label = trim($user->name);

                if (! $label) {
                    $label = $user->username ?: $user->email;
                }

                return [$user->getKey() => $label];
            })
            ->all();
        $types = collect(CustomerCardTypeEnum::values())
            ->mapWithKeys(fn (CustomerCardTypeEnum $enum) => [$enum->getValue() => $enum->label()])
            ->all();

        return view('plugins/hotel::customer-cards.create', compact('jsValidator', 'card', 'users', 'types'));
    }

    public function store(CustomerCardRequest $request)
    {
        $data = $request->validated();

        if ($request->filled('valid_until')) {
            $data['valid_until'] = Carbon::createFromFormat(BaseHelper::getDateFormat(), $request->input('valid_until'));
        }

        $data['is_active'] = $request->boolean('is_active');
        $data['assigned_to'] = $request->filled('assigned_to') ? $request->input('assigned_to') : null;
        $data['units_remaining'] = max(0, min($data['units_remaining'] ?? $data['units_total'], $data['units_total']));

        $card = CustomerCard::query()->create($data);

        event(new CreatedContentEvent(CUSTOMER_CARD_MODULE_SCREEN_NAME, $request, $card));

        return $this
            ->httpResponse()
            ->setMessage(trans('plugins/hotel::customer-card.created_message'))
            ->setNextUrl(route('customer-cards.edit', $card));
    }

    public function edit(CustomerCard $customerCard)
    {
        $this->pageTitle(trans('plugins/hotel::customer-card.form.edit', ['name' => $customerCard->name]));

        Assets::addScripts(['form-validation'])
            ->addScriptsDirectly('vendor/core/plugins/hotel/js/customer-card.js');

        $jsValidator = JsValidator::formRequest(CustomerCardRequest::class);
        $users = User::query()
            ->get()
            ->mapWithKeys(function (User $user) {
                $label = trim($user->name);

                if (! $label) {
                    $label = $user->username ?: $user->email;
                }

                return [$user->getKey() => $label];
            })
            ->all();
        $types = collect(CustomerCardTypeEnum::values())
            ->mapWithKeys(fn (CustomerCardTypeEnum $enum) => [$enum->getValue() => $enum->label()])
            ->all();

        return view('plugins/hotel::customer-cards.edit', [
            'card' => $customerCard,
            'jsValidator' => $jsValidator,
            'users' => $users,
            'types' => $types,
        ]);
    }

    public function update(CustomerCard $customerCard, CustomerCardRequest $request)
    {
        $data = $request->validated();

        if ($request->filled('valid_until')) {
            $data['valid_until'] = Carbon::createFromFormat(BaseHelper::getDateFormat(), $request->input('valid_until'));
        } else {
            $data['valid_until'] = null;
        }

        $data['is_active'] = $request->boolean('is_active');
        $data['assigned_to'] = $request->filled('assigned_to') ? $request->input('assigned_to') : null;
        $data['units_remaining'] = max(0, min($data['units_remaining'] ?? $customerCard->units_total, $data['units_total']));

        $customerCard->update($data);

        event(new UpdatedContentEvent(CUSTOMER_CARD_MODULE_SCREEN_NAME, $request, $customerCard));

        return $this
            ->httpResponse()
            ->withUpdatedSuccessMessage();
    }

    public function destroy(CustomerCard $customerCard)
    {
        return DeleteResourceAction::make($customerCard);
    }
}

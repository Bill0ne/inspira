<?php

namespace Botble\Hotel\Http\Controllers;

use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Facades\Assets;
use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Facades\BaseHelper;
use Botble\Hotel\Enums\CustomerCardTypeEnum;
use Botble\Hotel\Http\Requests\CustomerCardRequest;
use Botble\Hotel\Models\CustomerCard;
use Botble\Hotel\Models\Customer;
use Botble\Hotel\Services\CustomerCardService;
use Botble\Hotel\Supports\HotelSupport;
use Botble\Hotel\Tables\CustomerCardTable;
use Botble\JsValidation\Facades\JsValidator;
use Botble\Courses\Models\Course;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

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
        $card = new CustomerCard([
            'is_active' => true,
            'type' => CustomerCardTypeEnum::CUSTOM,
        ]);

        return view('plugins/hotel::customer-cards.create', [
            'jsValidator' => $jsValidator,
            'card' => $card,
            'customers' => $this->getCustomersList(),
            'types' => $this->getTypes(),
        ]);
    }

    public function store(CustomerCardRequest $request)
    {
        $data = $request->validated();

        $data['valid_until'] = $this->parseValidUntil($request);
        $data['is_active'] = $request->boolean('is_active');
        $data['assigned_to'] = $request->filled('assigned_to') ? (int) $request->input('assigned_to') : null;
        $data['created_by'] = $request->user()->getKey();
        $data['units_remaining'] = (int) Arr::get($data, 'units_total', 0);

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

        return view('plugins/hotel::customer-cards.edit', [
            'card' => $customerCard,
            'jsValidator' => $jsValidator,
            'customers' => $this->getCustomersList(),
            'types' => $this->getTypes(),
        ]);
    }

    public function update(CustomerCard $customerCard, CustomerCardRequest $request)
    {
        $data = $request->validated();

        $data['valid_until'] = $this->parseValidUntil($request);
        $data['is_active'] = $request->boolean('is_active');
        $data['assigned_to'] = $request->filled('assigned_to') ? (int) $request->input('assigned_to') : null;

        $customerCard->fill($data);

        if ($customerCard->units_remaining > $customerCard->units_total) {
            $customerCard->units_remaining = $customerCard->units_total;
        }

        if (! $customerCard->is_active || $customerCard->units_remaining <= 0) {
            $customerCard->is_active = false;
        }

        $customerCard->save();

        event(new UpdatedContentEvent(CUSTOMER_CARD_MODULE_SCREEN_NAME, $request, $customerCard));

        return $this
            ->httpResponse()
            ->withUpdatedSuccessMessage();
    }

    public function destroy(CustomerCard $customerCard)
    {
        event(new DeletedContentEvent(CUSTOMER_CARD_MODULE_SCREEN_NAME, request(), $customerCard));

        return DeleteResourceAction::make($customerCard);
    }

    public function assignToCustomer(CustomerCard $customerCard, int $customerId)
    {
        $customer = Customer::query()->findOrFail($customerId);
        $customerCard->update(['assigned_to' => $customer->getKey()]);

        return $this
            ->httpResponse()
            ->setMessage(trans('plugins/hotel::customer-card.assigned_message'))
            ->setNextUrl(route('customer-cards.edit', $customerCard));
    }

    public function apply(Request $request, CustomerCardService $service)
    {
        $card = $service->getValidCard((int) $request->input('card_id'), auth('customer')->id());
        $courseId = (int) $request->input('course_id');

        if (! $card || ! $service->isApplicable($card, $courseId)) {
            return $this->httpResponse()->setError()->setMessage(__('Ungültige Karte'));
        }

        $course = class_exists(Course::class) ? Course::query()->find($courseId) : null;
        $unitsUsed = min($card->units_total, 1);
        $discount = $service->calculateDiscount($card, $course, $unitsUsed);

        $data = HotelSupport::getCheckoutData();
        $data['customer_card_id'] = $card->getKey();
        $data['customer_card_discount'] = $discount;
        $data['customer_card_units_used'] = $unitsUsed;
        HotelSupport::saveCheckoutData($data);

        return $this->httpResponse()
            ->setMessage(__('Karte angewendet.'))
            ->setData(['discount' => format_price($discount)]);
    }

    public function remove()
    {
        $data = HotelSupport::getCheckoutData();
        unset($data['customer_card_id'], $data['customer_card_discount'], $data['customer_card_units_used']);
        HotelSupport::saveCheckoutData($data);

        return $this->httpResponse()->setMessage(__('Karte entfernt.'));
    }

    protected function parseValidUntil(Request $request): ?Carbon
    {
        if (! $request->filled('valid_until')) {
            return null;
        }

        return Carbon::createFromFormat(BaseHelper::getDateFormat(), $request->input('valid_until'));
    }

    protected function getCustomersList(): array
    {
        return Customer::query()
            ->select(['id', 'first_name', 'last_name', 'email'])
            ->get()
            ->mapWithKeys(function (Customer $customer) {
                $name = trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? ''));

                if (! $name) {
                    $name = $customer->email;
                }

                return [$customer->getKey() => $name];
            })
            ->all();
    }

    protected function getTypes(): array
    {
        return collect(CustomerCardTypeEnum::values())
            ->mapWithKeys(fn (CustomerCardTypeEnum $enum) => [$enum->getValue() => $enum->label()])
            ->all();
    }
}

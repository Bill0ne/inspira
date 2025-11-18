<?php

namespace Botble\Hotel\Http\Controllers;

use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Facades\Assets;
use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Hotel\Enums\CustomerCardTypeEnum;
use Botble\Hotel\Http\Requests\CustomerCardRequest;
use Botble\Hotel\Models\CustomerCard;
use Botble\Hotel\Models\Customer;
use Botble\Hotel\Models\CustomerCardUsage;
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
        $this->pageTitle(trans('plugins/hotel::customer-card.assignments.title'));

        Assets::addScriptsDirectly([
            'vendor/core/plugins/hotel/js/customer-card.js',
        ]);

        return $table
            ->showAssignedOnly()
            ->renderTable();
    }

    public function templates(CustomerCardTable $table)
    {
        $this->pageTitle(trans('plugins/hotel::customer-card.templates.title'));

        Assets::addScriptsDirectly([
            'vendor/core/plugins/hotel/js/customer-card.js',
        ]);

        return $table
            ->showAssignedOnly(false)
            ->renderTable();
    }

    public function create(Request $request)
    {
        $this->pageTitle(trans('plugins/hotel::customer-card.form.create'));

        Assets::addScripts(['form-validation']);

        Assets::addScriptsDirectly([
            'vendor/core/plugins/hotel/js/customer-card.js',
        ]);

        $jsValidator = JsValidator::formRequest(CustomerCardRequest::class);
        $card = new CustomerCard([
            'is_active' => true,
            'type' => CustomerCardTypeEnum::CUSTOM,
        ]);

        $showAssignmentForm = $request->boolean('assigned');

        return view('plugins/hotel::customer-cards.create', [
            'jsValidator' => $jsValidator,
            'card' => $card,
            'customers' => $this->getCustomersList(),
            'types' => $this->getTypes(),
            'isAssigned' => false,
            'templates' => $showAssignmentForm ? $this->getTemplatesList() : collect(),
            'showAssignmentForm' => $showAssignmentForm,
        ]);
    }

    public function store(CustomerCardRequest $request)
    {
        $data = $request->validated();

        if ($request->filled('template_id')) {
            $template = CustomerCard::query()
                ->whereNull('assigned_to')
                ->findOrFail((int) $request->input('template_id'));

            $card = $template->replicate();
            $card->fill([
                'uid' => null,
                'assigned_to' => (int) $request->input('assigned_to'),
                'valid_until' => $this->parseValidUntil($request) ?? $template->valid_until,
                'is_active' => $request->boolean('is_active', true),
                'created_by' => $request->user()->getKey(),
            ]);

            $card->units_remaining = $template->units_total;
            $card->save();
        } else {
            $data['valid_until'] = $this->parseValidUntil($request);
            $data['is_active'] = $request->boolean('is_active');
            $data['is_single_purchase'] = $request->boolean('is_single_purchase');
            $data['assigned_to'] = $request->filled('assigned_to') ? (int) $request->input('assigned_to') : null;
            $data['created_by'] = $request->user()->getKey();
            $data['units_remaining'] = (int) Arr::get($data, 'units_total', 0);

            $card = CustomerCard::query()->create($data);
        }

        event(new CreatedContentEvent(CUSTOMER_CARD_MODULE_SCREEN_NAME, $request, $card));

        return $this
            ->httpResponse()
            ->setMessage(trans('plugins/hotel::customer-card.created_message'))
            ->setNextUrl(route('customer-cards.edit', $card));
    }

    public function edit(CustomerCard $customerCard)
    {
        $this->pageTitle(trans('plugins/hotel::customer-card.form.edit', ['name' => $customerCard->name]));

        Assets::addScripts(['form-validation']);

        Assets::addScriptsDirectly([
            'vendor/core/plugins/hotel/js/customer-card.js',
        ]);

        $jsValidator = JsValidator::formRequest(CustomerCardRequest::class);

        return view('plugins/hotel::customer-cards.edit', [
            'card' => $customerCard,
            'jsValidator' => $jsValidator,
            'customers' => $this->getCustomersList(),
            'types' => $this->getTypes(),
            'isAssigned' => (bool) $customerCard->assigned_to,
        ]);
    }

    public function update(CustomerCard $customerCard, CustomerCardRequest $request)
    {
        $data = $request->validated();

        $data['valid_until'] = $this->parseValidUntil($request);
        $data['is_active'] = $request->boolean('is_active');
        $data['is_single_purchase'] = $request->boolean('is_single_purchase');
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
        $customerId = auth('customer')->id();

        if (! $customerId) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(trans('plugins/hotel::customer-card.messages.login_required'));
        }

        $card = $service->getValidCard((int) $request->input('card_id'), $customerId);

        if (! $card) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(trans('plugins/hotel::customer-card.messages.card_not_found'));
        }

        $context = $this->resolveCheckoutContext($request);
        $courseId = $this->resolveCourseId($request, $context);

        if (! $service->isApplicable($card, $courseId)) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(trans('plugins/hotel::customer-card.messages.card_unavailable'));
        }

        $course = class_exists(Course::class) ? Course::query()->find($courseId) : null;
        $unitsUsed = min($card->units_remaining, 1);

        $coursePricing = 0.0;

        if ($course) {
            $pricing = $course->resolvePricing(auth('customer')->user());
            $coursePricing = (float) Arr::get(
                $pricing,
                'calculated_gross',
                $course->getPriceWithTax($course->getCourseTotalPrice())
            );
        }

        $discount = $service->calculateDiscount($card, $course, $unitsUsed, $coursePricing);

        $data = HotelSupport::getCheckoutData(context: $context) ?: [];
        $data['customer_card_id'] = $card->getKey();
        $data['customer_card_discount'] = $discount;
        $data['customer_card_units_used'] = $unitsUsed;
        HotelSupport::saveCheckoutData($data, $context);

        return $this->httpResponse()
            ->setMessage(__('Karte angewendet.'))
            ->setData([
                'discount' => format_price($discount),
                'raw_discount' => $discount,
                'card_id' => $card->getKey(),
                'units_used' => $unitsUsed,
            ]);
    }

    public function remove(Request $request)
    {
        $context = $this->resolveCheckoutContext($request);
        $data = HotelSupport::getCheckoutData(context: $context) ?: [];
        unset($data['customer_card_id'], $data['customer_card_discount'], $data['customer_card_units_used']);
        HotelSupport::saveCheckoutData($data, $context);

        return $this->httpResponse()
            ->setMessage(__('Karte entfernt.'))
            ->setData([
                'discount' => format_price(0),
                'raw_discount' => 0,
            ]);
    }

    public function usages(CustomerCard $customerCard, BaseHttpResponse $response): BaseHttpResponse
    {
        $orders = collect();
        $assignedCardIds = [$customerCard->getKey()];

        if (! $customerCard->assigned_to) {
            $orders = $customerCard->orders()
                ->where('status', 'completed')
                ->with([
                    'customer',
                    'assignedCard' => function ($query) {
                        $query
                            ->withCount('usages')
                            ->with(['usages' => function ($usageQuery) {
                                $usageQuery->latest()->limit(1);
                            }]);
                    },
                ])
                ->orderByDesc('completed_at')
                ->get();

            $assignedCardIds = $orders->pluck('assigned_card_id')->filter()->all();
        }

        $usages = CustomerCardUsage::query()
            ->whereIn('card_id', $assignedCardIds)
            ->with(['course', 'booking.room.room', 'card.customer'])
            ->orderByDesc('created_at')
            ->get();

        return $response->setData([
            'html' => view('plugins/hotel::customer-cards.partials.usages', compact('customerCard', 'usages', 'orders'))->render(),
        ]);
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

    protected function getTemplatesList()
    {
        return CustomerCard::query()
            ->whereNull('assigned_to')
            ->orderBy('name')
            ->get();
    }

    protected function resolveCheckoutContext(Request $request): string
    {
        if ($request->boolean('course_checkout') || $request->filled('course_id')) {
            return HotelSupport::CONTEXT_COURSE;
        }

        if (session('checkout_context') === HotelSupport::CONTEXT_COURSE) {
            return HotelSupport::CONTEXT_COURSE;
        }

        if (session()->has('course_checkout_token')) {
            return HotelSupport::CONTEXT_COURSE;
        }

        return HotelSupport::CONTEXT_HOTEL;
    }

    protected function resolveCourseId(Request $request, string $context): ?int
    {
        $courseId = (int) $request->input('course_id');

        if ($courseId) {
            return $courseId;
        }

        if ($context === HotelSupport::CONTEXT_COURSE) {
            $courseId = (int) (HotelSupport::getCheckoutData('course_id', HotelSupport::CONTEXT_COURSE) ?? 0);
        }

        return $courseId ?: null;
    }
}

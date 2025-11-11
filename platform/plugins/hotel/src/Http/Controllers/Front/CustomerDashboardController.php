<?php

namespace Botble\Hotel\Http\Controllers\Front;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Hotel\Models\CustomerCard;
use Botble\Hotel\Models\CustomerCardUsage;
use Botble\Hotel\Services\CustomerCardService;
use Botble\Theme\Facades\Theme;

class CustomerDashboardController
{
    public function cards(CustomerCardService $customerCardService)
    {
        $user = auth('customer')->user();

        $cards = CustomerCard::query()
            ->where('assigned_to', $user->getKey())
            ->orderByDesc('updated_at')
            ->get();

        $activeCard = $cards->first(function (CustomerCard $card) {
            return $card->is_active && $card->units_remaining > 0 && (! $card->valid_until || ! $card->valid_until->isPast());
        });

        $displayCard = $activeCard ?? $cards->first();

        $availableCards = CustomerCard::query()
            ->whereNull('assigned_to')
            ->where('is_active', true)
            ->active()
            ->orderBy('name')
            ->get()
            ->map(function (CustomerCard $card) use ($customerCardService) {
                $card->purchase_price = $customerCardService->calculatePurchasePrice($card);

                return $card;
            });

        $usages = CustomerCardUsage::query()
            ->whereIn('card_id', $cards->pluck('id'))
            ->orderByDesc('created_at')
            ->get();

        return Theme::scope(
            'hotel.customers.cards',
            compact('cards', 'availableCards', 'usages', 'activeCard', 'displayCard'),
            'plugins/hotel::themes.customers.cards'
        )->render();
    }

    public function checkoutCard(CustomerCard $customerCard, CustomerCardService $customerCardService)
    {
        abort_if($customerCard->assigned_to !== null, 404);
        abort_if(! $customerCard->is_active, 404);
        abort_if($customerCard->valid_until && $customerCard->valid_until->isPast(), 404);

        $user = auth('customer')->user();

        $purchasePrice = $customerCardService->calculatePurchasePrice($customerCard);

        return Theme::scope(
            'hotel.customers.card-checkout',
            compact('customerCard', 'purchasePrice', 'user'),
            'plugins/hotel::themes.customers.card-checkout'
        )->render();
    }

    public function purchaseCard(
        CustomerCard $customerCard,
        CustomerCardService $customerCardService,
        BaseHttpResponse $response
    ): BaseHttpResponse {
        abort_if($customerCard->assigned_to !== null, 404);
        abort_if(! $customerCard->is_active, 404);
        abort_if($customerCard->valid_until && $customerCard->valid_until->isPast(), 404);

        $user = auth('customer')->user();

        $customerCardService->assignTemplateToCustomer($customerCard, $user);

        return $response
            ->setNextUrl(route('customer.cards'))
            ->setMessage(trans('plugins/hotel::customer-card.purchase.success_message'));
    }
}

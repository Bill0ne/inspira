<?php

namespace Botble\Hotel\Http\Controllers\Front;

use Botble\Hotel\Models\CustomerCard;
use Botble\Hotel\Models\CustomerCardUsage;
use Botble\Theme\Facades\Theme;

class CustomerDashboardController
{
    public function cards()
    {
        $user = auth('customer')->user();

        $cards = CustomerCard::query()
            ->where('assigned_to', $user->getKey())
            ->orderBy('valid_until')
            ->get();

        $availableCards = CustomerCard::query()
            ->whereNull('assigned_to')
            ->where('is_active', true)
            ->active()
            ->orderBy('name')
            ->get();

        $usages = CustomerCardUsage::query()
            ->whereIn('card_id', $cards->pluck('id'))
            ->orderByDesc('created_at')
            ->get();

        return Theme::scope(
            'hotel.customers.cards',
            compact('cards', 'availableCards', 'usages'),
            'plugins/hotel::themes.customers.cards'
        )->render();
    }
}

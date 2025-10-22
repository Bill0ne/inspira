<?php

namespace Botble\PriceConfigurator\Http\Middleware;

use Closure;
use Botble\PriceConfigurator\Services\PriceConfigurator;
use Botble\Hotel\Facades\HotelHelper;
use Botble\Hotel\Models\Room;

class AdjustBookingJson
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if (method_exists($response, 'getData')) {
            $data = $response->getData(true);
            $pc = app(PriceConfigurator::class);
            $customer = HotelHelper::getCurrentCustomer();
            $category = $customer?->customer_category ?? 'STANDARD';

            foreach ($data['items'] ?? [] as &$item) {
                if (!isset($item['room_id'], $item['base_total'])) continue;
                $room = Room::find($item['room_id']);
                $hours = $item['hours'] ?? null;

                $item['total'] = $pc->adjustRoomTotalPrice(
                    $room,
                    (float) $item['base_total'],
                    $category,
                    ['hours' => $hours, 'customer_id' => $customer?->id]
                );
            }
            $response->setData($data);
        }

        return $response;
    }
}

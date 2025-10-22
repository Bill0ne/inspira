<?php

namespace Botble\PriceConfigurator\Http\Controllers;

use Botble\Hotel\Http\Controllers\PublicController as BasePublicController;
use Botble\Hotel\Facades\HotelHelper;
use Botble\PriceConfigurator\Services\PriceConfigurator;
use Botble\Hotel\Models\Room;
use Illuminate\Http\Request;
use Botble\Base\Http\Responses\BaseHttpResponse;

class PublicController extends BasePublicController
{
    public function __construct(
        protected \Botble\Hotel\Services\GetRoomService $getRoomService,
        protected PriceConfigurator $priceConfigurator
    ) {
        parent::__construct($getRoomService);
    }

    public function ajaxCalculateBookingAmount(Request $request, BaseHttpResponse $response)
    {
        $startDate = HotelHelper::dateFromRequest($request->input('start_date'));
        $endDate = HotelHelper::dateFromRequest($request->input('end_date'));
        $numberOfRooms = $request->input('rooms', 1);

        /** @var Room $room */
        $room = Room::query()->findOrFail($request->input('room_id'));

        $nights = $startDate->diffInDays($endDate);

        $room->total_price = $room->getRoomTotalPrice($startDate, $endDate, $numberOfRooms);

        $customerCategory = HotelHelper::getCurrentCustomer()?->customer_category ?? 'STANDARD';
        $room->total_price = $this->priceConfigurator->adjustRoomTotalPrice($room, (float) $room->total_price, $customerCategory);

        [$amount, $discountAmount] = $this->calculateBookingAmount(
            $room,
            $request->input('services', []),
            $nights,
            $numberOfRooms,
            $request->input('foods', [])
        );

        $taxAmount = $room->tax->percentage * ($amount - $discountAmount) / 100;
        $totalAmount = ($amount - $discountAmount) + $taxAmount;

        return $response
            ->setData([
                'amount' => $amount,
                'discount_amount' => $discountAmount,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'amount_text' => format_price($amount),
                'discount_amount_text' => format_price($discountAmount),
                'tax_amount_text' => format_price($taxAmount),
                'total_amount_text' => format_price($totalAmount),
            ]);
    }

    public function postCheckout(Request $request, BaseHttpResponse $response)
    {
        $startDate = HotelHelper::dateFromRequest($request->input('start_date'));
        $endDate = HotelHelper::dateFromRequest($request->input('end_date'));
        $numberOfRooms = $request->input('rooms', 1);

        $room = Room::query()->findOrFail($request->input('room_id'));
        $room->total_price = $room->getRoomTotalPrice($startDate, $endDate, $numberOfRooms);

        $customerCategory = HotelHelper::getCurrentCustomer()?->customer_category ?? 'STANDARD';
        $room->total_price = $this->priceConfigurator->adjustRoomTotalPrice($room, (float) $room->total_price, $customerCategory);

        return parent::postCheckout($request, $response);
    }
}

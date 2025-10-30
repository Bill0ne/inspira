@php
    $route ??= 'invoices.generate';
    $isLoggedIn = auth('customer')->check() || auth()->check();
@endphp

@if ($booking)
    <x-core::datagrid class="mb-4">
        <x-core::datagrid.item :title="__('Booking Number')">
            {{ $booking->booking_number }}
        </x-core::datagrid.item>

        <x-core::datagrid.item :title="__('Time')">
            {{ $booking->created_at }}
        </x-core::datagrid.item>

        <x-core::datagrid.item :title="__('Full Name')">
            {{ $booking->address->last_name }}
        </x-core::datagrid.item>

        <x-core::datagrid.item :title="__('Email')">
            <a href="mailto:{{ $booking->address->email }}">{{ $booking->address->email }}</a>
        </x-core::datagrid.item>

        @if ($booking->address->phone)
            <x-core::datagrid.item :title="__('Phone')">
                <a href="tel:{{ $booking->address->phone }}">{{ $booking->address->phone }}</a>
            </x-core::datagrid.item>
        @endif

        @if ($booking->address->full_address)
            <x-core::datagrid.item :title="__('Address')">
                {{ $booking->address->full_address }}
            </x-core::datagrid.item>
        @endif
    </x-core::datagrid>

    <x-core::datagrid class="mb-4">
        {{-- Room Name --}}
        <x-core::datagrid.item :title="__('Room')">
            @if ($booking->rooms->isNotEmpty())
                @php
                    $room = $booking->rooms->first()->room ?? null;
                @endphp

                @if ($room && $room->exists)
                    <a href="{{ $room->url }}">
                        {{ $room->name }}
                    </a>
                @else
                    {{ $booking->rooms->first()->room_name }}
                @endif
            @endif
        </x-core::datagrid.item>

        {{-- Start and End Dates --}}
        <x-core::datagrid.item :title="__('Stay Duration')">
            <div class="space-y-1 mt-1 text-sm text-gray-800">
                @foreach ($booking->rooms as $roomBooking)
                    @php
                        $start = \Carbon\Carbon::parse($roomBooking->start_date);
                        $end = \Carbon\Carbon::parse($roomBooking->end_date);
                    @endphp

                    <div class="flex items-center justify-between border-b border-gray-200 pb-1">
                        @if ($start->isSameDay($end))
                            {{-- Same day: show date once, and time range --}}
                            <span>
                        {{ $start->format('M d, Y') }}
                        <span class="text-gray-500 mx-2"></span>
                        {{ $start->format('h:i A') }}
                        <span class="text-gray-500 mx-1">→</span>
                        {{ $end->format('h:i A') }}
                    </span>
                        @else
                            {{-- Different days: show full date-times --}}
                            <span>
                        {{ $start->format('M d, Y h:i A') }}
                        <span class="text-gray-500 mx-2">→</span>
                        {{ $end->format('M d, Y h:i A') }}
                    </span>
                        @endif
                    </div>
                @endforeach
            </div>
        </x-core::datagrid.item>


    @if ($booking->arrival_time)
            <x-core::datagrid.item :title="__('Arrival Time')">
                {{ $booking->arrival_time }}
            </x-core::datagrid.item>
        @endif

        @if ($booking->requests)
            <x-core::datagrid.item :title="__('Requests')">
                {{ $booking->requests }}
            </x-core::datagrid.item>
        @endif

        @if ($booking->number_of_guests)
            <x-core::datagrid.item :title="__('Number of adults')">
                {{ $booking->number_of_guests }}
            </x-core::datagrid.item>
        @endif

        @if ($booking->number_of_children)
            <x-core::datagrid.item :title="__('Number of children')">
                {{ $booking->number_of_children }}
            </x-core::datagrid.item>
        @endif
    </x-core::datagrid>

    <div class="mb-4">
        <h4>{{ __('Room') }}</h4>
        <x-core::table>
            <x-core::table.header>
                <x-core::table.header.cell class="text-center" style="width: 150px;">
                    {{ __('Image') }}
                </x-core::table.header.cell>
                <x-core::table.header.cell>
                    {{ __('Name') }}
                </x-core::table.header.cell>
                <x-core::table.header.cell class="text-center">
                    {{ __('Checkin Date') }}
                </x-core::table.header.cell>
                <x-core::table.header.cell class="text-center">
                    {{ __('Checkout Date') }}
                </x-core::table.header.cell>
                <x-core::table.header.cell class="text-center">
                    {{ __('Number of rooms') }}
                </x-core::table.header.cell>
                <x-core::table.header.cell class="text-center">
                    {{ __('Price') }}
                </x-core::table.header.cell>
            </x-core::table.header>
            <x-core::table.body>
                @foreach ($booking->rooms as $roomBooking)
                    <x-core::table.body.row>
                        @php
                            $room = $roomBooking->room ?? null;
                        @endphp

                        @if ($room && $room->exists)
                            <x-core::table.body.cell class="text-center" style="width: 150px; vertical-align: middle !important;">
                                <a href="{{ $room->url }}" target="_blank">
                                    <img src="{{ RvMedia::getImageUrl($room->image, 'thumb', false, RvMedia::getDefaultImage()) }}"
                                         alt="{{ $roomBooking->room_name }}" width="140">
                                </a>
                            </x-core::table.body.cell>
                            <x-core::table.body.cell style="vertical-align: middle !important;">
                                <a class="booking-information-link" href="{{ $room->url }}" target="_blank">
                                    {{ $room->name }}
                                </a>
                            </x-core::table.body.cell>
                        @else
                            <x-core::table.body.cell>
                                <img src="{{ RvMedia::getImageUrl($roomBooking->room_image, 'thumb', false, RvMedia::getDefaultImage()) }}"
                                     alt="{{ $roomBooking->room_name }}" width="140">
                            </x-core::table.body.cell>
                            <x-core::table.body.cell style="vertical-align: middle !important;">
                                {{ $roomBooking->room_name }}
                            </x-core::table.body.cell>
                        @endif
                        <x-core::table.body.cell class="text-center" style="vertical-align: middle !important;">
                            {{ \Carbon\Carbon::parse($roomBooking->start_date)->format('M d, Y h:i A') }}
                        </x-core::table.body.cell>

                        <x-core::table.body.cell class="text-center" style="vertical-align: middle !important;">
                            {{ \Carbon\Carbon::parse($roomBooking->end_date)->format('M d, Y h:i A') }}
                        </x-core::table.body.cell>
                        <x-core::table.body.cell class="text-center" style="vertical-align: middle !important;">
                            {{ $roomBooking->number_of_rooms }}
                        </x-core::table.body.cell>

                        @if($isLoggedIn)
                            <x-core::table.body.cell class="text-center" style="vertical-align: middle !important;">
                                <strong>{{ format_price($roomBooking->price) }}</strong>
                            </x-core::table.body.cell>
                        @else
                            <x-core::table.body.cell class="text-center" colspan="2">
                                {{ __('Preise werden nach Login angezeigt') }}
                            </x-core::table.body.cell>
                        @endif
                    </x-core::table.body.row>
                @endforeach
            </x-core::table.body>
        </x-core::table>
    </div>

    @if ($booking->services->count())
        <h4>{{ __('Services') }}</h4>
        <x-core::table>
            <x-core::table.header>
                <x-core::table.header.cell>{{ __('Name') }}</x-core::table.header.cell>
                <x-core::table.header.cell class="text-center">{{ __('Price') }}</x-core::table.header.cell>
                <x-core::table.header.cell class="text-center">{{ __('Total') }}</x-core::table.header.cell>
            </x-core::table.header>
            <x-core::table.body>
                @foreach ($booking->services->unique() as $service)
                    <x-core::table.body.row>
                        <x-core::table.body.cell style="vertical-align: middle !important;">
                            {{ $service->name }}
                        </x-core::table.body.cell>
                        @if($isLoggedIn)
                            <x-core::table.body.cell class="text-center">
                                {{ format_price($service->price) }} x {{ $booking->room->number_of_rooms }}
                            </x-core::table.body.cell>
                            <x-core::table.body.cell class="text-center">
                                {{ format_price($service->price * $booking->room->number_of_rooms) }}
                            </x-core::table.body.cell>
                        @else
                            <x-core::table.body.cell class="text-center" colspan="2">
                                {{ __('Preise werden nach Login angezeigt') }}
                            </x-core::table.body.cell>
                        @endif
                    </x-core::table.body.row>
                @endforeach
            </x-core::table.body>
        </x-core::table>
        <br>
    @endif

    @if ($booking->foods->count())
        <h4>{{ __('Foods') }}</h4>
        <x-core::table>
            <x-core::table.header>
                <x-core::table.header.cell>{{ __('Name') }}</x-core::table.header.cell>
                <x-core::table.header.cell class="text-center">{{ __('Price') }}</x-core::table.header.cell>
                <x-core::table.header.cell class="text-center">{{ __('Total') }}</x-core::table.header.cell>
            </x-core::table.header>
            <x-core::table.body>
                @foreach ($booking->foods->unique() as $food)
                    <x-core::table.body.row>
                        <x-core::table.body.cell style="vertical-align: middle !important;">
                            {{ $food->name }}
                        </x-core::table.body.cell>
                        @if($isLoggedIn)
                            <x-core::table.body.cell class="text-center">
                                {{ format_price($food->price) }}
                            </x-core::table.body.cell>
                            <x-core::table.body.cell class="text-center">
                                {{ format_price($food->price) }}
                            </x-core::table.body.cell>
                        @else
                            <x-core::table.body.cell class="text-center" colspan="2">
                                {{ __('Preise werden nach Login angezeigt') }}
                            </x-core::table.body.cell>
                        @endif
                    </x-core::table.body.row>
                @endforeach
            </x-core::table.body>
        </x-core::table>
        <br>
    @endif

    <x-core::datagrid>
        @if($isLoggedIn)
{{--            @if($booking->rule_discount > 0)--}}
{{--                <x-core::datagrid.item :title="__('Automatic Discount')">--}}
{{--                    {{ format_price($booking->rule_discount) }}--}}
{{--                </x-core::datagrid.item>--}}
{{--            @endif--}}
            <x-core::datagrid.item :title="__('Sub Total')">
                {{ format_price($booking->sub_total) }}
            </x-core::datagrid.item>
            <x-core::datagrid.item :title="__('Rule Price')">
                {{ format_price($booking->rule_discount) }}
            </x-core::datagrid.item>
            <x-core::datagrid.item :title="__('Discount Amount')">
                {{ format_price($booking->coupon_amount) }}
            </x-core::datagrid.item>
            <x-core::datagrid.item :title="__('Tax Amount')">
                {{ format_price($booking->tax_amount) }}
            </x-core::datagrid.item>
            <x-core::datagrid.item :title="__('Total Amount')">
                {{ format_price($booking->amount) }}
            </x-core::datagrid.item>
        @else
            <x-core::datagrid.item :title="__('Preisinformationen')">
                {{ __('werden nach Login angezeigt') }}
            </x-core::datagrid.item>
        @endif

        <x-core::datagrid.item :title="__('Status')">
            {!! $booking->status->toHtml() !!}
        </x-core::datagrid.item>

        @if (is_plugin_active('payment') && $booking->payment->id)
            @auth
                <x-core::datagrid.item :title="__('Payment ID')">
                    <a href="{{ route('payment.show', $booking->payment->id) }}" target="_blank">
                        {{ $booking->payment->charge_id }}
                        <x-core::icon name="ti ti-external-link" />
                    </a>
                </x-core::datagrid.item>
            @endauth

            <x-core::datagrid.item :title="__('Payment method')">
                {{ $booking->payment->payment_channel->label() }}
            </x-core::datagrid.item>

            <x-core::datagrid.item :title="__('Payment status')">
                {!! $booking->payment->status->toHtml() !!}
            </x-core::datagrid.item>

            @if ($booking->payment->payment_channel == \Botble\Payment\Enums\PaymentMethodEnum::BANK_TRANSFER
                && $booking->payment->status == \Botble\Payment\Enums\PaymentStatusEnum::PENDING
            )
                <x-core::datagrid.item :title="__('Payment info')">
                    {!! BaseHelper::clean(get_payment_setting('description', $booking->payment->payment_channel)) !!}
                </x-core::datagrid.item>
            @endif

            @if ($displayBookingStatus ?? false)
                <x-core::datagrid.item :title="__('Booking status')">
                    {!! $booking->status->toHtml() !!}
                </x-core::datagrid.item>
            @endif
        @endif
    </x-core::datagrid>

    @if ((auth()->check() || $booking->customer_id) && ($invoiceId = $booking->invoice->id) && $route)
        <div class="btn-list mt-5">
            <x-core::button
                tag="a"
                :href="route($route, ['invoice' => $invoiceId, 'type' => 'print'])"
                target="_blank"
                icon="ti ti-printer"
                :class="$buttonClass ?? ''"
            >
                {{ __('View Invoice') }}
            </x-core::button>
            <x-core::button
                tag="a"
                :href="route($route, ['invoice' => $invoiceId, 'type' => 'download'])"
                target="_blank"
                icon="ti ti-download"
                :class="$buttonClass ?? ''"
            >
                {{ __('Download Invoice') }}
            </x-core::button>
        </div>
    @endif
@endif

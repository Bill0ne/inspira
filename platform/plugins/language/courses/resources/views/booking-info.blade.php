@php
    $route ??= 'invoices.generate';
    $isLoggedIn = auth('customer')->check() || auth()->check();
@endphp

<x-core::datagrid class="mb-4">
    <x-core::datagrid.item :title="__('Booking Number')">
        {{ $booking->booking_number }}
    </x-core::datagrid.item>

    <x-core::datagrid.item :title="__('Time')">
        {{ $booking->created_at->format('d M Y H:i') }}
    </x-core::datagrid.item>

    @if ($booking->customer && $booking->customer->id)
        {{-- Logged-in customer --}}
        <x-core::datagrid.item :title="__('Full Name')">
            {{ $booking->customer->first_name }} {{ $booking->customer->last_name }}
        </x-core::datagrid.item>

        <x-core::datagrid.item :title="__('Email')">
            <a href="mailto:{{ $booking->customer->email }}">{{ $booking->customer->email }}</a>
        </x-core::datagrid.item>

        @if ($booking->customer->phone)
            <x-core::datagrid.item :title="__('Phone')">
                <a href="tel:{{ $booking->customer->phone }}">{{ $booking->customer->phone }}</a>
            </x-core::datagrid.item>
        @endif

        @if ($booking->customer->address)
            <x-core::datagrid.item :title="__('Address')">
                {{ $booking->customer->address }}
            </x-core::datagrid.item>
        @endif
    @elseif ($booking->address)
        {{-- Guest booking (address info only) --}}
        <x-core::datagrid.item :title="__('Full Name')">
            {{ $booking->address->first_name }} {{ $booking->address->last_name }}
        </x-core::datagrid.item>

        @if ($booking->address->email)
            <x-core::datagrid.item :title="__('Email')">
                <a href="mailto:{{ $booking->address->email }}">{{ $booking->address->email }}</a>
            </x-core::datagrid.item>
        @endif

        @if ($booking->address->phone)
            <x-core::datagrid.item :title="__('Phone')">
                <a href="tel:{{ $booking->address->phone }}">{{ $booking->address->phone }}</a>
            </x-core::datagrid.item>
        @endif

        @if ($booking->address->address)
            <x-core::datagrid.item :title="__('Address')">
                {{ $booking->address->address }}
                @if ($booking->address->city)
                    , {{ $booking->address->city }}
                @endif
                @if ($booking->address->country)
                    , {{ $booking->address->country }}
                @endif
            </x-core::datagrid.item>
        @endif
    @endif
</x-core::datagrid>


{{-- Course Detail --}}
@if ($booking->course)
    <x-core::datagrid class="mt-4">
        <x-core::datagrid.item :title="__('Course Name')">
            {{ $booking->course->name }}
        </x-core::datagrid.item>

        <x-core::datagrid.item :title="__('Price')">
            {{ format_price($booking->sub_total) }}
        </x-core::datagrid.item>

        @if ($booking->course->duration)
            <x-core::datagrid.item :title="__('Duration')">
                {{ $booking->course->duration }}
            </x-core::datagrid.item>
        @endif

        @if ($booking->course->description)
            <x-core::datagrid.item :title="__('Description')">
                {{ Str::limit(strip_tags($booking->course->description), 120) }}
            </x-core::datagrid.item>
        @endif
    </x-core::datagrid>
@endif

{{-- Session Detail --}}
@if ($booking->session)
    <x-core::datagrid class="mt-4">
        <x-core::datagrid.item :title="__('Session Date')">
            {{ $booking->session->start_date->format('d M Y h:i A') }}
            @if($booking->session->end_date)
                – {{ $booking->session->end_date->format('d M Y h:i A') }}
            @endif
        </x-core::datagrid.item>

        <x-core::datagrid.item :title="__('Seats Booked')">
            1 {{-- assuming 1 seat per booking; adjust if you allow multiple --}}
        </x-core::datagrid.item>
    </x-core::datagrid>
@endif

{{-- Booking Amounts --}}
<x-core::datagrid class="mt-4">
{{--    @if ($booking->rule_discount > 0)--}}
{{--        <x-core::datagrid.item :title="__('Automatic Discount')">--}}
{{--            {{ format_price($booking->rule_discount) }}--}}
{{--        </x-core::datagrid.item>--}}
{{--    @endif--}}
    <x-core::datagrid.item :title="__('Sub Total')">
        {{ format_price($booking->sub_total) }}
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

    <x-core::datagrid.item :title="__('Status')">
        {!! $booking->status->toHtml() !!}
    </x-core::datagrid.item>
</x-core::datagrid>

{{-- Course Instructor Info --}}
@if ($booking->course && $booking->course->instructor)
    <x-core::datagrid class="mt-4">
        <x-core::datagrid.item :title="__('Instructor Name')">
            {{ $booking->course->instructor->name }}
        </x-core::datagrid.item>

        <x-core::datagrid.item :title="__('Instructor Email')">
            <a href="mailto:{{ $booking->course->instructor->email }}">
                {{ $booking->course->instructor->email }}
            </a>
        </x-core::datagrid.item>

        @if ($booking->course->instructor->phone)
            <x-core::datagrid.item :title="__('Instructor Phone')">
                <a href="tel:{{ $booking->course->instructor->phone }}">
                    {{ $booking->course->instructor->phone }}
                </a>
            </x-core::datagrid.item>
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

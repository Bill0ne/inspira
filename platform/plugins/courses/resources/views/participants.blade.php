<table class="table table-bordered">
    <thead>
    <tr>
        <th>#</th>
        <th>{{ __('Name') }}</th>
        <th>{{ __('E-mail') }}</th>
        <th>{{ __('Phone') }}</th>
        <th>Zahlungsstatus</th>
        <th>{{ __('Action') }}</th>
    </tr>
    </thead>
    <tbody>
    @forelse ($bookings as $index => $booking)
        <tr>
            <td>{{ $index + 1 }}</td>
            @if ($booking->customer && $booking->customer->id)
                <td>{{ $booking->customer->first_name }} {{ $booking->customer->last_name }}</td>
            @elseif ($booking->address)
                <td>{{ $booking->address->first_name }} {{ $booking->address->last_name }}</td>
            @else
                <td>{{ __('N/A') }}</td>
            @endif
            @if ($booking->customer && $booking->customer->email)
                <td>{{ $booking->customer->email }}</td>
            @elseif ($booking->address && $booking->address->email)
                <td>{{ $booking->address->email }}</td>
            @else
                <td>{{ __('N/A') }}</td>
            @endif
            @if ($booking->customer && $booking->customer->phone)
                <td>{{ $booking->customer->phone }}</td>
            @elseif ($booking->address && $booking->address->phone)
                <td>{{ $booking->address->phone }}</td>
            @else
                <td>{{ __('N/A') }}</td>
            @endif
            <td>{!! $booking->payment?->status->toHtml() ?? __('N/A') !!}</td>
            <td>
                <a href="{{ route('course-booking.edit', $booking->id) }}" class="btn btn-sm btn-primary">
                    {{ __('View') }}
                </a>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="text-center">{{ __('No participants found.') }}</td>
        </tr>
    @endforelse
    </tbody>
</table>

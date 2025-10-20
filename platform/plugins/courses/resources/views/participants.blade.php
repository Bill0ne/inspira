<table class="table table-bordered">
    <thead>
    <tr>
        <th>#</th>
        <th>Name</th>
        <th>E-mail</th>
        <th>Telefon</th>
        <th>Zahlungsstatus</th>
        <th>Aktion</th>
    </tr>
    </thead>
    <tbody>
    @forelse ($bookings as $index => $booking)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $booking->customer->name ?? 'N/A' }}</td>
            <td>{{ $booking->customer->email ?? 'N/A' }}</td>
            <td>{{ $booking->customer->phone ?? 'N/A' }}</td>
            <td>{!!$booking->payment?->status->toHtml() !!}</td>
            <td>
                <a href="{{ route('course-booking.edit', $booking->id) }}" class="btn btn-sm btn-primary">
                    Sicht
                </a>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="text-center">No participants found.</td>
        </tr>
    @endforelse
    </tbody>
</table>

<table class="table table-bordered mb-0">
    <thead>
    <tr>
        <th>Name</th>
        <th>E-Mail</th>
        <th>Buchungsdatum</th>
        <th>Zahlungsstatus</th>
        <th>Aktionen</th>
    </tr>
    </thead>
    <tbody>
    @forelse ($participants as $p)
        <tr>
            <td>{{ $p['name'] }}</td>
            <td>{{ $p['email'] }}</td>
            <td>{{ $p['booking_date'] }}</td>
            <td>{!! $p['payment_status'] !!}</td>
            <td>
                <a href="{{ route('course-booking.edit', $p['booking_id']) }}" class="btn btn-sm btn-primary">
                    Sicht
                </a>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="5" class="text-center">Noch keine Teilnehmer.</td>
        </tr>
    @endforelse
    </tbody>
</table>

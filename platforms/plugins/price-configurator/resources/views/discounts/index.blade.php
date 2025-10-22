@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title">Mengen- & Spezialrabatte</h4>
            <a href="{{ route('pc.discounts.create') }}" class="btn btn-primary">Neu</a>
        </div>
        <div class="card-body table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Titel</th>
                        <th>Bedingung</th>
                        <th>Bereich</th>
                        <th>Typ</th>
                        <th>Wert</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Aktionen</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($items as $i)
                    <tr>
                        <td>{{ $i->title }}</td>
                        <td>{{ strtoupper($i->condition_type) }}</td>
                        <td>{{ $i->range_min ?? '–' }} – {{ $i->range_max ?? '∞' }}</td>
                        <td>{{ strtoupper($i->discount_type) }}</td>
                        <td>{{ $i->discount_value }}</td>
                        <td>{{ $i->priority }}</td>
                        <td>
                            <span class="badge {{ $i->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                {{ $i->status }}
                            </span>
                        </td>
                        <td class="d-flex gap-2">
                            <a href="{{ route('pc.discounts.edit', $i->id) }}" class="btn btn-sm btn-warning">Bearbeiten</a>
                            <form action="{{ route('pc.discounts.toggle', $i->id) }}" method="POST" style="display:inline-block">
                                @csrf
                                <button class="btn btn-sm btn-info" type="submit">Toggle</button>
                            </form>
                            <form action="{{ route('pc.discounts.destroy', $i->id) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Löschen?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger" type="submit">Löschen</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center">Keine Einträge</td></tr>
                @endforelse
                </tbody>
            </table>
            {{ $items->links() }}
        </div>
    </div>
@endsection

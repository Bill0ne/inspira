@extends('core/base::layouts.master')

@section('content')
    <div class="max-w-full">
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <div class="mb-3">
            <a href="{{ route('pc.rules.create') }}" class="btn btn-primary">Neue Regel</a>
        </div>

        <div class="card">
            <div class="card-header"><h5>Regeln</h5></div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead><tr><th>ID</th><th>Tier</th><th>Kundenkat.</th><th>Scope</th><th>Typ</th><th>Wert</th><th>Status</th><th>Aktionen</th></tr></thead>
                    <tbody>
                        @forelse($items as $it)
                            <tr>
                                <td>{{ $it->id }}</td>
                                <td>{{ $it->price_tier_id }}</td>
                                <td>{{ $it->customer_category }}</td>
                                <td>{{ $it->scope }}</td>
                                <td>{{ $it->calculation_type }}</td>
                                <td>{{ $it->calculation_value }}</td>
                                <td>
                                    <form method="POST" action="{{ route('pc.rules.toggle', $it->id) }}">
                                        @csrf
                                        <button class="btn btn-xs {{ $it->status === 'active' ? 'btn-success' : 'btn-warning' }}">
                                            {{ $it->status }}
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('pc.rules.edit', $it->id) }}">Bearbeiten</a>
                                    <form method="POST" action="{{ route('pc.rules.destroy', $it->id) }}" style="display:inline-block" onsubmit="return confirm('Löschen?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Löschen</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8">Keine Regeln vorhanden.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-3">{{ $items->links() }}</div>
            </div>
        </div>
    </div>
@endsection

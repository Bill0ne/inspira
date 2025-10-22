@extends('core/base::layouts.master')

@section('content')
    <div class="max-w-full">
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <div class="mb-3">
            <a href="{{ route('pc.tiers.create') }}" class="btn btn-primary">Neue Stufe</a>
        </div>

        <div class="card">
            <div class="card-header"><h5>Preisstufen</h5></div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead><tr><th>ID</th><th>Name</th><th>Prio</th><th>Status</th><th>Exklusiv</th><th>Zeitraum</th><th>Aktionen</th></tr></thead>
                    <tbody>
                        @forelse($items as $it)
                            <tr>
                                <td>{{ $it->id }}</td>
                                <td>{{ $it->name }}</td>
                                <td>{{ $it->priority }}</td>
                                <td>
                                    <form method="POST" action="{{ route('pc.tiers.toggle', $it->id) }}">
                                        @csrf
                                        <button class="btn btn-xs {{ $it->status === 'active' ? 'btn-success' : 'btn-warning' }}">
                                            {{ $it->status }}
                                        </button>
                                    </form>
                                </td>
                                <td>{{ $it->is_exclusive ? 'ja' : 'nein' }}</td>
                                <td>{{ $it->starts_at ?? '-' }} – {{ $it->ends_at ?? '-' }}</td>
                                <td>
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('pc.tiers.edit', $it->id) }}">Bearbeiten</a>
                                    <form method="POST" action="{{ route('pc.tiers.destroy', $it->id) }}" style="display:inline-block" onsubmit="return confirm('Löschen? Regeln werden mitgelöscht!')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Löschen</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7">Keine Stufen vorhanden.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-3">{{ $items->links() }}</div>
            </div>
        </div>
    </div>
@endsection

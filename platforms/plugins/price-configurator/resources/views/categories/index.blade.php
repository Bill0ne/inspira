@extends('core/base::layouts.master')

@section('content')
    <div class="max-w-full">
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <div class="mb-3">
            <a href="{{ route('pc.categories.create') }}" class="btn btn-primary">Neue Kategorie</a>
        </div>

        <div class="card">
            <div class="card-header"><h5>Kundenkategorien</h5></div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead><tr><th>ID</th><th>Code</th><th>Label</th><th>Status</th><th>Aktionen</th></tr></thead>
                    <tbody>
                        @forelse($items as $it)
                            <tr>
                                <td>{{ $it->id }}</td>
                                <td>{{ $it->code }}</td>
                                <td>{{ $it->label }}</td>
                                <td>
                                    <form method="POST" action="{{ route('pc.categories.toggle', $it->id) }}">
                                        @csrf
                                        <button class="btn btn-xs {{ $it->status === 'active' ? 'btn-success' : 'btn-warning' }}">
                                            {{ $it->status }}
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('pc.categories.edit', $it->id) }}">Bearbeiten</a>
                                    <form method="POST" action="{{ route('pc.categories.destroy', $it->id) }}" style="display:inline-block" onsubmit="return confirm('Löschen?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Löschen</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5">Keine Kategorien vorhanden.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-3">{{ $items->links() }}</div>
            </div>
        </div>
    </div>
@endsection

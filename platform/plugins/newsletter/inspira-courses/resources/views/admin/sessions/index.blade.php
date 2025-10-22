@extends('core/base::layouts.master')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Termine</h3>
  <a href="{{ route('inspira-courses.sessions.create') }}" class="btn btn-primary">Neu</a>
</div>
<table class="table table-striped">
  <thead><tr><th>ID</th><th>Kurs</th><th>Start</th><th>Ort</th><th>Preis</th><th>Kapazität</th><th>Verkauft</th><th>Aktionen</th></tr></thead>
  <tbody>
  @foreach($items as $row)
    <tr>
      <td>{{ $row->id }}</td>
      <td>{{ optional($row->course)->name }}</td>
      <td>{{ optional($row->starts_at)->format('d.m.Y H:i') }}</td>
      <td>{{ $row->location }}</td>
      <td>{{ format_price($row->price) }}</td>
      <td>{{ $row->capacity }}</td>
      <td>{{ $row->seats_sold }}</td>
      <td class="text-nowrap">
        <a href="{{ route('inspira-courses.sessions.edit', $row->id) }}" class="btn btn-sm btn-warning">Bearbeiten</a>
        <form action="{{ route('inspira-courses.sessions.destroy', $row->id) }}" method="POST" class="d-inline">
          @csrf @method('DELETE')
          <button class="btn btn-sm btn-danger" onclick="return confirm('Löschen?')">Löschen</button>
        </form>
      </td>
    </tr>
  @endforeach
  </tbody>
</table>
{{ $items->links() }}
@endsection

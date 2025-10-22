@extends('core/base::layouts.master')

@section('content')
<h3>Teilnehmer / Käufe</h3>
<table class="table table-striped">
  <thead><tr><th>ID</th><th>Kurs</th><th>Termin</th><th>Menge</th><th>Betrag</th><th>Status</th><th>Aktionen</th></tr></thead>
  <tbody>
  @foreach($items as $row)
    <tr>
      <td>{{ $row->id }}</td>
      <td>{{ optional($row->session->course)->name }}</td>
      <td>{{ optional($row->session->starts_at)->format('d.m.Y H:i') }}</td>
      <td>{{ $row->qty }}</td>
      <td>{{ format_price($row->amount) }} {{ $row->currency }}</td>
      <td>{{ $row->status }}</td>
      <td class="text-nowrap">
        <a href="{{ route('inspira-courses.purchases.show', $row->id) }}" class="btn btn-sm btn-primary">Details</a>
        <form action="{{ route('inspira-courses.purchases.destroy', $row->id) }}" method="POST" class="d-inline">
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

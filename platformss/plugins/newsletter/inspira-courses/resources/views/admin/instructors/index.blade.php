@extends('core/base::layouts.master')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Kurshalter</h3>
  <a href="{{ route('inspira-courses.instructors.create') }}" class="btn btn-primary">Neu</a>
</div>
<table class="table table-striped">
  <thead><tr><th>ID</th><th>Name</th><th>E-Mail</th><th>Telefon</th><th>Aktionen</th></tr></thead>
  <tbody>
  @foreach($items as $row)
    <tr>
      <td>{{ $row->id }}</td>
      <td>{{ $row->name }}</td>
      <td>{{ $row->email }}</td>
      <td>{{ $row->phone }}</td>
      <td class="text-nowrap">
        <a href="{{ route('inspira-courses.instructors.edit', $row->id) }}" class="btn btn-sm btn-warning">Bearbeiten</a>
        <form action="{{ route('inspira-courses.instructors.destroy', $row->id) }}" method="POST" class="d-inline">
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

@extends('core/base::layouts.master')

@section('content')
<h3>Termin bearbeiten</h3>
<form method="POST" action="{{ route('inspira-courses.sessions.update', $item->id) }}">
  @csrf @method('PUT')
  
<div class="mb-3">
  <label class="form-label">Kurs *</label>
  <select name="course_id" class="form-select" required>
    @foreach($courses as $id=>$name)
      <option value="{{ $id }}" @selected(old('course_id', $item->course_id ?? null) == $id)>{{ $name }}</option>
    @endforeach
  </select>
</div>
<div class="mb-3">
  <label class="form-label">Start *</label>
  <input type="datetime-local" name="starts_at" class="form-control"
         value="{{ old('starts_at', isset($item) && $item->starts_at ? $item->starts_at->format('Y-m-d\\TH:i') : '') }}" required>
</div>
<div class="mb-3">
  <label class="form-label">Ende</label>
  <input type="datetime-local" name="ends_at" class="form-control"
         value="{{ old('ends_at', isset($item) && $item->ends_at ? $item->ends_at->format('Y-m-d\\TH:i') : '') }}">
</div>
<div class="mb-3">
  <label class="form-label">Ort</label>
  <input type="text" name="location" class="form-control" value="{{ old('location', $item->location ?? '') }}">
</div>
<div class="mb-3">
  <label class="form-label">Kapazität *</label>
  <input type="number" name="capacity" class="form-control" value="{{ old('capacity', $item->capacity ?? 0) }}" required>
</div>
<div class="mb-3">
  <label class="form-label">Preis *</label>
  <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $item->price ?? 0) }}" required>
</div>

  <button class="btn btn-success">Aktualisieren</button>
  <a href="{{ route('inspira-courses.sessions.index') }}" class="btn btn-secondary">Zurück</a>
</form>
@endsection

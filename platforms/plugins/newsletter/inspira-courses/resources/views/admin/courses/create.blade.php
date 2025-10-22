@extends('core/base::layouts.master')

@section('content')
<h3>Kurs anlegen</h3>
<form method="POST" action="{{ route('inspira-courses.courses.store') }}">
  @csrf
  
<div class="mb-3">
  <label class="form-label">Name *</label>
  <input type="text" name="name" class="form-control" value="{{ old('name', $item->name ?? '') }}" required>
</div>
<div class="mb-3">
  <label class="form-label">Slug *</label>
  <input type="text" name="slug" class="form-control" value="{{ old('slug', $item->slug ?? '') }}" required>
</div>
<div class="mb-3">
  <label class="form-label">Kurshalter</label>
  <select name="instructor_id" class="form-select">
    <option value="">{{ __('-- wählen --') }}</option>
    @foreach($instructors as $id=>$name)
      <option value="{{ $id }}" @selected(old('instructor_id', $item->instructor_id ?? null) == $id)>{{ $name }}</option>
    @endforeach
  </select>
</div>
<div class="mb-3">
  <label class="form-label">Beschreibung</label>
  <textarea name="description" class="form-control" rows="5">{{ old('description', $item->description ?? '') }}</textarea>
</div>

  <button class="btn btn-success">Speichern</button>
  <a href="{{ route('inspira-courses.courses.index') }}" class="btn btn-secondary">Abbrechen</a>
</form>
@endsection

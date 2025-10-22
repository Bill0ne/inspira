@extends('core/base::layouts.master')

@section('content')
<h3>Kurshalter bearbeiten</h3>
<form method="POST" action="{{ route('inspira-courses.instructors.update', $item->id) }}">
  @csrf @method('PUT')
  <div class="mb-3">
    <label class="form-label">Name *</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $item->name) }}" required>
  </div>
  <div class="mb-3">
    <label class="form-label">E-Mail</label>
    <input type="email" name="email" class="form-control" value="{{ old('email', $item->email) }}">
  </div>
  <div class="mb-3">
    <label class="form-label">Telefon</label>
    <input type="text" name="phone" class="form-control" value="{{ old('phone', $item->phone) }}">
  </div>
  <div class="mb-3">
    <label class="form-label">Bio</label>
    <textarea name="bio" class="form-control" rows="5">{{ old('bio', $item->bio) }}</textarea>
  </div>
  <button class="btn btn-success">Aktualisieren</button>
  <a href="{{ route('inspira-courses.instructors.index') }}" class="btn btn-secondary">Zurück</a>
</form>
@endsection

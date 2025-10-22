@extends('core/base::layouts.master')

@section('content')
<h3>Kurshalter anlegen</h3>
<form method="POST" action="{{ route('inspira-courses.instructors.store') }}">
  @csrf
  <div class="mb-3">
    <label class="form-label">Name *</label>
    <input type="text" name="name" class="form-control" required>
  </div>
  <div class="mb-3">
    <label class="form-label">E-Mail</label>
    <input type="email" name="email" class="form-control">
  </div>
  <div class="mb-3">
    <label class="form-label">Telefon</label>
    <input type="text" name="phone" class="form-control">
  </div>
  <div class="mb-3">
    <label class="form-label">Bio</label>
    <textarea name="bio" class="form-control" rows="5"></textarea>
  </div>
  <button class="btn btn-success">Speichern</button>
  <a href="{{ route('inspira-courses.instructors.index') }}" class="btn btn-secondary">Abbrechen</a>
</form>
@endsection

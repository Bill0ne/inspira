@extends('core/base::layouts.master')

@section('content')
    <div class="card">
        <div class="card-header"><h5>{{ isset($item) ? 'Kategorie bearbeiten' : 'Neue Kategorie' }}</h5></div>
        <div class="card-body">
            <form method="POST" action="{{ isset($item) ? route('pc.categories.update', $item->id) : route('pc.categories.store') }}">
                @csrf
                @if (isset($item)) @method('PUT') @endif

                <div class="form-group mb-3">
                    <label>Code *</label>
                    <input type="text" name="code" class="form-control" required value="{{ old('code', $item->code ?? '') }}" placeholder="z. B. VIP">
                </div>
                <div class="form-group mb-3">
                    <label>Label *</label>
                    <input type="text" name="label" class="form-control" required value="{{ old('label', $item->label ?? '') }}" placeholder="z. B. VIP">
                </div>
                <div class="form-group mb-3">
                    <label>Status *</label>
                    <select name="status" class="form-control">
                        <option value="active" @selected(old('status', $item->status ?? 'active') === 'active')>active</option>
                        <option value="inactive" @selected(old('status', $item->status ?? 'active') === 'inactive')>inactive</option>
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label>Beschreibung</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $item->description ?? '') }}</textarea>
                </div>

                <button class="btn btn-primary">Speichern</button>
            </form>
        </div>
    </div>
@endsection

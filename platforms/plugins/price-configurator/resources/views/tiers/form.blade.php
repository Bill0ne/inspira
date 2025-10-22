@extends('core/base::layouts.master')

@section('content')
    <div class="card">
        <div class="card-header"><h5>{{ isset($item) ? 'Stufe bearbeiten' : 'Neue Stufe' }}</h5></div>
        <div class="card-body">
            <form method="POST" action="{{ isset($item) ? route('pc.tiers.update', $item->id) : route('pc.tiers.store') }}">
                @csrf
                @if (isset($item)) @method('PUT') @endif

                <div class="form-group mb-3">
                    <label>Name *</label>
                    <input type="text" name="name" class="form-control" required value="{{ old('name', $item->name ?? '') }}">
                </div>
                <div class="form-group mb-3">
                    <label>Priorität</label>
                    <input type="number" name="priority" class="form-control" value="{{ old('priority', $item->priority ?? 100) }}">
                </div>
                <div class="form-group mb-3">
                    <label>Status *</label>
                    <select name="status" class="form-control">
                        <option value="active" @selected(old('status', $item->status ?? 'active') === 'active')>active</option>
                        <option value="inactive" @selected(old('status', $item->status ?? 'active') === 'inactive')>inactive</option>
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label>Exklusiv?</label>
                    <input type="checkbox" name="is_exclusive" value="1" @checked(old('is_exclusive', $item->is_exclusive ?? false))>
                </div>
                <div class="form-group mb-3">
                    <label>Start</label>
                    <input type="datetime-local" name="starts_at" class="form-control" value="{{ old('starts_at', isset($item->starts_at) ? \Carbon\Carbon::parse($item->starts_at)->format('Y-m-d\TH:i') : '') }}">
                </div>
                <div class="form-group mb-3">
                    <label>Ende</label>
                    <input type="datetime-local" name="ends_at" class="form-control" value="{{ old('ends_at', isset($item->ends_at) ? \Carbon\Carbon::parse($item->ends_at)->format('Y-m-d\TH:i') : '') }}">
                </div>
                <div class="form-group mb-3">
                    <label>Notizen</label>
                    <textarea name="notes" class="form-control" rows="3">{{ old('notes', $item->notes ?? '') }}</textarea>
                </div>

                <button class="btn btn-primary">Speichern</button>
            </form>
        </div>
    </div>
@endsection

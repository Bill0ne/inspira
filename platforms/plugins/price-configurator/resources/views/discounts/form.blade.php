@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="card">
        <div class="card-header"><h4 class="card-title">Rabatt {{ $item->id ? 'bearbeiten' : 'anlegen' }}</h4></div>
        <div class="card-body">
            <form method="POST" action="{{ $item->id ? route('pc.discounts.update', $item->id) : route('pc.discounts.store') }}">
                @csrf
                @if($item->id) @method('PUT') @endif

                <div class="mb-3">
                    <label class="form-label">Titel</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $item->title) }}" required>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Bedingung</label>
                        <select name="condition_type" class="form-select">
                            <option value="hours" {{ old('condition_type', $item->condition_type)=='hours'?'selected':'' }}>Stunden</option>
                            <option value="bookings" {{ old('condition_type', $item->condition_type)=='bookings'?'selected':'' }}>Buchungen</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Min</label>
                        <input type="number" name="range_min" class="form-control" value="{{ old('range_min', $item->range_min) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Max</label>
                        <input type="number" name="range_max" class="form-control" value="{{ old('range_max', $item->range_max) }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Rabatt-Typ</label>
                        <select name="discount_type" class="form-select">
                            <option value="absolute" {{ old('discount_type', $item->discount_type)=='absolute'?'selected':'' }}>Absolut (€)</option>
                            <option value="percent" {{ old('discount_type', $item->discount_type)=='percent'?'selected':'' }}>% Prozent</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Wert</label>
                        <input type="number" step="0.01" name="discount_value" class="form-control" value="{{ old('discount_value', $item->discount_value) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Priority</label>
                        <input type="number" name="priority" class="form-control" value="{{ old('priority', $item->priority ?? 0) }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Apply to</label>
                        <select name="apply_to" class="form-select">
                            <option value="all" {{ old('apply_to', $item->apply_to)=='all'?'selected':'' }}>Alle</option>
                            <option value="room" {{ old('apply_to', $item->apply_to)=='room'?'selected':'' }}>Raum (vorbereitet)</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ old('status', $item->status)=='active'?'selected':'' }}>Aktiv</option>
                            <option value="inactive" {{ old('status', $item->status)=='inactive'?'selected':'' }}>Inaktiv</option>
                        </select>
                    </div>
                </div>

                <div class="mt-3">
                    <button class="btn btn-primary">Speichern</button>
                    <a class="btn btn-secondary" href="{{ route('pc.discounts.index') }}">Abbrechen</a>
                </div>
            </form>
        </div>
    </div>
@endsection

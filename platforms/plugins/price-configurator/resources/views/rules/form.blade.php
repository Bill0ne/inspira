@extends('core/base::layouts.master')

@section('content')
    <div class="card">
        <div class="card-header"><h5>{{ isset($item) ? 'Regel bearbeiten' : 'Neue Regel' }}</h5></div>
        <div class="card-body">
            <form method="POST" action="{{ isset($item) ? route('pc.rules.update', $item->id) : route('pc.rules.store') }}">
                @csrf
                @if (isset($item)) @method('PUT') @endif

                <div class="form-group mb-3">
                    <label>Preisstufe *</label>
                    <select name="price_tier_id" class="form-control" required>
                        @foreach($tiers as $t)
                            <option value="{{ $t->id }}" @selected(old('price_tier_id', $item->price_tier_id ?? '') == $t->id)>{{ $t->name }} (Prio {{ $t->priority }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label>Kundenkategorie *</label>
                    <select name="customer_category" class="form-control">
                        @foreach($categories as $c)
                            <option value="{{ $c->code }}" @selected(old('customer_category', $item->customer_category ?? '') == $c->code)>{{ $c->code }} — {{ $c->label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label>Scope *</label>
                    <select name="scope" class="form-control" onchange="document.getElementById('rcat').style.display = (this.value==='by_room_category') ? 'block' : 'none';">
                        <option value="all_rooms" @selected(old('scope', $item->scope ?? 'all_rooms') === 'all_rooms')>Alle Zimmer</option>
                        <option value="by_room_category" @selected(old('scope', $item->scope ?? '') === 'by_room_category')>Nach Zimmerkategorie</option>
                    </select>
                </div>

                <div id="rcat" style="display: {{ old('scope', $item->scope ?? 'all_rooms') === 'by_room_category' ? 'block' : 'none' }};">
                    <div class="form-group mb-3">
                        <label>Zimmerkategorien</label>
                        <select name="room_category_ids[]" class="form-control" multiple size="8">
                            @foreach(DB::table('ht_room_categories')->orderBy('name')->get() as $rc)
                                <option value="{{ $rc->id }}" @if(isset($selected) && in_array($rc->id, $selected)) selected @endif>{{ $rc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label>Berechnungsmodus *</label>
                    <select name="calculation_type" class="form-control">
                        <option value="percent" @selected(old('calculation_type', $item->calculation_type ?? 'percent') === 'percent')>Prozent (%)</option>
                        <option value="absolute" @selected(old('calculation_type', $item->calculation_type ?? '') === 'absolute')>Absolut (€)</option>
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label>Wert *</label>
                    <input type="number" step="0.01" name="calculation_value" class="form-control" required value="{{ old('calculation_value', $item->calculation_value ?? 0) }}">
                </div>

                <div class="form-group mb-3">
                    <label>Rundungsmodus *</label>
                    <select name="rounding_mode" class="form-control">
                        @foreach(['none','up','down','bankers'] as $rm)
                            <option value="{{ $rm }}" @selected(old('rounding_mode', $item->rounding_mode ?? 'none') === $rm)>{{ $rm }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label>Runden auf *</label>
                    <input type="number" step="0.01" name="round_to" class="form-control" required value="{{ old('round_to', $item->round_to ?? 0.01) }}">
                </div>

                <div class="form-group mb-3">
                    <label>Status *</label>
                    <select name="status" class="form-control">
                        <option value="active" @selected(old('status', $item->status ?? 'active') === 'active')>active</option>
                        <option value="inactive" @selected(old('status', $item->status ?? 'active') === 'inactive')>inactive</option>
                    </select>
                </div>

                <button class="btn btn-primary">Speichern</button>
            </form>
        </div>
    </div>
@endsection

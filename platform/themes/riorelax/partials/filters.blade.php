@php
    // Seitentyp automatisch erkennen
    $isCourses = request()->is('*courses*');
    $isRooms = request()->is('*rooms*');
@endphp

<div class="filter-bar py-3 mb-4 border-bottom bg-white shadow-sm rounded-2">
    <div class="container">
        <form id="mainFilterForm" method="GET" action="{{ url()->current() }}" class="row g-3 align-items-center">

            {{-- 🔍 Suchfeld --}}
            <div class="col-md-3">
                <div class="position-relative">
                    <i class="fal fa-search position-absolute"
                       style="top:50%;left:12px;transform:translateY(-50%);color:#578E88;"></i>
                    <input type="text" name="search" class="form-control ps-4"
                           placeholder="Suche nach {{ $isCourses ? 'Kurs oder Trainer' : 'Raum' }}..."
                           value="{{ request('search') }}">
                </div>
            </div>

            {{-- 🧭 Kategorie --}}
            <div class="col-md-2">
                <select name="category" class="form-select">
                    <option value="">Kategorie</option>
                    @foreach(($categories ?? []) as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- 👩‍🏫 Trainer (nur bei Kursen) --}}
            @if($isCourses)
                <div class="col-md-2">
                    <select name="trainer" class="form-select">
                        <option value="">Trainer</option>
                        @foreach(($trainers ?? []) as $trainer)
                            <option value="{{ $trainer->id }}" {{ request('trainer') == $trainer->id ? 'selected' : '' }}>
                                {{ $trainer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            {{-- 🗓️ Datum / Zeitraum --}}
            @if($isCourses)
                <div class="col-md-2">
                    <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                </div>
            @else
                <div class="col-md-2 d-flex gap-2">
                    <input type="date" name="start" class="form-control" value="{{ request('start') }}" placeholder="Von">
                    <input type="date" name="end" class="form-control" value="{{ request('end') }}" placeholder="Bis">
                </div>
            @endif

            {{-- 💶 Preis (nur bei Räumen) --}}
            @if($isRooms)
                <div class="col-md-2">
                    <select name="price" class="form-select">
                        <option value="">Preisbereich</option>
                        <option value="low" {{ request('price') == 'low' ? 'selected' : '' }}>Bis 50€</option>
                        <option value="medium" {{ request('price') == 'medium' ? 'selected' : '' }}>50–100€</option>
                        <option value="high" {{ request('price') == 'high' ? 'selected' : '' }}>Über 100€</option>
                    </select>
                </div>
            @endif

            {{-- 🔽 Sortierung --}}
            <div class="col-md-2">
                <select name="sort" class="form-select">
                    <option value="">Sortieren nach</option>
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Neueste</option>
                    <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Beliebteste</option>
                    @if($isCourses)
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Preis aufsteigend</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Preis absteigend</option>
                    @endif
                </select>
            </div>

            {{-- ♻️ Reset --}}
            <div class="col-md-1 text-end">
                <a href="{{ url()->current() }}" class="text-muted small text-decoration-none">Zurücksetzen</a>
            </div>
        </form>
    </div>
</div>

<style>
    .filter-bar select, .filter-bar input[type="text"], .filter-bar input[type="date"] {
        height: 42px;
        border: 1px solid #DDD;
        border-radius: 6px;
        font-size: 14px;
    }

    .filter-bar select:focus, .filter-bar input:focus {
        border-color: #578E88;
        box-shadow: 0 0 0 2px rgba(87, 142, 136, 0.2);
    }
</style>

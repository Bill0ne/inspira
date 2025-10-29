@php
    $isCourses = request()->is('*courses*');
    $isRooms   = request()->is('*rooms*');
@endphp

<div class="filter-bar-wrapper border-bottom mb-4">
    {{-- 🔘 Toggle nur auf Mobile sichtbar --}}
    <div class="filter-toggle d-lg-none py-2 text-center text-muted" onclick="document.getElementById('filterBar').classList.toggle('open')">
        <i class="fal fa-sliders-h me-2"></i> Filter anzeigen / ausblenden
    </div>

    <div id="filterBar" class="filter-bar bg-white py-3 px-3 px-md-4 shadow-sm rounded-2">
        <form id="mainFilterForm" method="GET" action="{{ url()->current() }}" class="row g-3 align-items-center">

            {{-- 🔍 Suchfeld (links) --}}
            <div class="col-lg-3 col-md-4 col-sm-12 d-flex align-items-center">
                <i class="fal fa-sliders-h me-2 text-muted fs-5"></i>
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Suche {{ $isCourses ? 'nach Kurs oder Trainer' : 'nach Raum' }}"
                       value="{{ request('search') }}">
            </div>

            {{-- 🧭 Kategorie --}}
            <div class="col-lg-2 col-md-4 col-sm-6">
                <select name="category" class="form-select form-select-sm text-muted">
                    <option value="">Kategorie</option>
                    @foreach(($categories ?? []) as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- 👩‍🏫 Trainer nur bei Kursen --}}
            @if($isCourses)
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <select name="trainer" class="form-select form-select-sm text-muted">
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
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <input type="date" name="date" class="form-control form-control-sm text-muted" value="{{ request('date') }}">
                </div>
            @else
                <div class="col-lg-3 col-md-6 col-sm-12 d-flex gap-2">
                    <input type="date" name="start" class="form-control form-control-sm text-muted" value="{{ request('start') }}" placeholder="Von">
                    <input type="date" name="end" class="form-control form-control-sm text-muted" value="{{ request('end') }}" placeholder="Bis">
                </div>
            @endif

            {{-- 💶 Preis nur bei Räumen --}}
            @if($isRooms)
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <select name="price" class="form-select form-select-sm text-muted">
                        <option value="">Preis</option>
                        <option value="low" {{ request('price') == 'low' ? 'selected' : '' }}>Bis 50 €</option>
                        <option value="medium" {{ request('price') == 'medium' ? 'selected' : '' }}>50–100 €</option>
                        <option value="high" {{ request('price') == 'high' ? 'selected' : '' }}>Über 100 €</option>
                    </select>
                </div>
            @endif

            {{-- 🔽 Sortierung --}}
            <div class="col-lg-2 col-md-4 col-sm-6">
                <select name="sort" class="form-select form-select-sm text-muted">
                    <option value="">Sortieren nach</option>
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Neueste</option>
                    <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Beliebteste</option>
                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Preis aufsteigend</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Preis absteigend</option>
                </select>
            </div>

            {{-- 🔢 Anzahl rechts --}}
            <div class="col-lg-1 col-md-2 text-end text-muted small">
                <span>
    {{ isset($courses) ? ($courses->total() ?? 0) : (isset($rooms) ? ($rooms->total() ?? 0) : 0) }}
    Ergebnisse
</span>

            </div>
        </form>
    </div>
</div>

<style>
    /* Farben & Typo weicher */
    .filter-bar-wrapper, .filter-bar {
        font-size: 13px;
        color: #444;
    }
    .filter-bar input, .filter-bar select {
        border: 1px solid #ddd;
        color: #555;
        font-size: 13px;
    }
    .filter-bar input::placeholder { color: #aaa; }

    /* Mobile Toggle */
    #filterBar {
        transition: max-height 0.3s ease;
        overflow: hidden;
    }
    #filterBar:not(.open) {
        max-height: 0;
        padding: 0;
        border: none;
    }
    #filterBar.open {
        max-height: 600px;
    }

    @media (min-width: 992px) {
        #filterBar { max-height: none !important; overflow: visible; }
        .filter-toggle { display: none; }
    }
</style>

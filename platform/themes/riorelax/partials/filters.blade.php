@php
    $type = $filterType ?? null;
    if (! in_array($type, ['courses', 'rooms'], true)) {
        $type = request()->query('filter_type');
    }

    if (! in_array($type, ['courses', 'rooms'], true)) {
        $type = 'courses';
    }

    $isCourses = $type === 'courses';
    $isRooms   = $type === 'rooms';

    // Falls Controller keine Daten liefert, hole sie hier fallback-mäßig
    $categories = $categories ?? (
        $isCourses
            ? \Botble\Courses\Models\CourseCategory::query()
                ->wherePublished()
                ->whereHas('courses', fn ($q) => $q->wherePublished())
                ->orderBy('name')
                ->get()
            : \Botble\Hotel\Models\RoomCategory::query()
                ->wherePublished()
                ->whereHas('rooms', fn ($q) => $q->wherePublished())
                ->orderBy('name')
                ->get()
    );

    $trainers = $trainers ?? (
        $isCourses
            ? \Botble\Courses\Models\Instructor::query()
                ->wherePublished()
                ->whereHas('courses', fn ($q) => $q->wherePublished())
                ->orderBy('name')
                ->get()
            : collect()
    );

    $activeFilters = collect([
        request('search'),
        request('category'),
        request('date'),
        request('trainer'),
        request('sort'),
    ])->filter(fn ($value) => filled($value));

    $resultCount = isset($courses)
        ? ($courses->total() ?? 0)
        : (isset($rooms) ? ($rooms->total() ?? 0) : \Theme\Riorelax\Helpers\FilterHelper::count(request(), $type));
@endphp

<div class="filter-bar-wrapper mb-4">
    {{-- 🔘 Mobile Toggle --}}
    <button type="button"
            id="filterToggle"
            class="filter-toggle-btn btn btn-light d-lg-none w-100 d-flex justify-content-center align-items-center"
            aria-controls="filterBar"
            aria-expanded="{{ $activeFilters->isNotEmpty() ? 'true' : 'false' }}">
        <i class="fal fa-filter me-2"></i>
        <span>{{ $activeFilters->isNotEmpty() ? 'Filter ausblenden' : 'Filter anzeigen' }}</span>
        @if($activeFilters->count())
            <span class="badge bg-primary ms-2">{{ $activeFilters->count() }}</span>
        @endif
    </button>

    <div id="filterBar" class="filter-bar shadow-sm rounded-3 {{ $activeFilters->isNotEmpty() ? 'open' : '' }}">
        <form id="mainFilterForm" method="GET" action="{{ url()->current() }}" class="row g-3 align-items-center">
            <input type="hidden" name="filter_type" value="{{ $type }}">

            {{-- 🔍 Suche --}}
            <div class="col-lg-3 col-md-6 col-sm-12">
                <label class="visually-hidden" for="filter-search">Suche</label>
                <div class="search-field d-flex align-items-center">
                    <i class="fal fa-search me-2 text-muted fs-5"></i>
                    <input id="filter-search" type="text" name="search" class="form-control form-control-sm"
                           placeholder="Suche {{ $isCourses ? 'nach Kurs oder Coach' : 'nach Raum' }}"
                           value="{{ request('search') }}">
                </div>
            </div>

            {{-- 🧭 Kategorie --}}
            <div class="col-lg-3 col-md-6 col-sm-6">
                <label class="visually-hidden" for="filter-category">Kategorie</label>
                <select id="filter-category" name="category" class="form-select form-select-sm text-muted">
                    <option value="">{{ $isCourses ? 'Kurskategorie' : 'Raumkategorie' }}</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- 🗓️ Datum (nur 1 Feld: Wann) --}}
            <div class="col-lg-2 col-md-6 col-sm-6">
                <label class="visually-hidden" for="filter-date">Wann</label>
                <input id="filter-date" type="date" name="date" class="form-control form-control-sm text-muted"
                       value="{{ request('date') }}">
            </div>

            {{-- 👩‍🏫 Coach (nur bei Kursen) --}}
            @if($isCourses)
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <label class="visually-hidden" for="filter-trainer">Coach</label>
                    <select id="filter-trainer" name="trainer" class="form-select form-select-sm text-muted">
                        <option value="">Coach</option>
                        @foreach($trainers as $trainer)
                            <option value="{{ $trainer->id }}" {{ request('trainer') == $trainer->id ? 'selected' : '' }}>
                                {{ $trainer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            {{-- 🔽 Sortierung --}}
            <div class="col-lg-2 col-md-6 col-sm-6">
                <label class="visually-hidden" for="filter-sort">Sortieren</label>
                <select id="filter-sort" name="sort" class="form-select form-select-sm text-muted">
                    <option value="">Sortieren nach</option>
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Neueste</option>
                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Älteste</option>
                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Preis aufsteigend</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Preis absteigend</option>
                </select>
            </div>

            {{-- 🔢 Ergebniszahl rechts --}}
            <div class="col-lg-1 col-md-6 text-lg-end text-muted small">
                <span>{{ number_format($resultCount) }} Ergebnisse</span>
            </div>
        </form>
    </div>
</div>

{{-- 🧠 Auto-Submit --}}
<script>
(function(){
  const f = document.getElementById('mainFilterForm');
  if(!f) return;
  const toggle = document.getElementById('filterToggle');
  const bar = document.getElementById('filterBar');
  if(toggle && bar){
    toggle.addEventListener('click', function(){
      const isOpen = bar.classList.toggle('open');
      toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      const textEl = toggle.querySelector('span');
      if(textEl){
        textEl.textContent = isOpen ? 'Filter ausblenden' : 'Filter anzeigen';
      }
    });
  }
  f.querySelectorAll('select,input[type="date"]').forEach(el => {
    el.addEventListener('change', ()=>f.submit());
  });
  const s = f.querySelector('input[name="search"]');
  if(s) s.addEventListener('keydown', e=>{ if(e.key==='Enter') f.submit(); });
})();
</script>

<style>
.filter-bar-wrapper, .filter-bar {
    font-size: 13px;
    color: #444;
}
.filter-bar {
    background-color: #F4F4F4;
    padding: 1.25rem 1.5rem;
    border: 1px solid #e0e0e0;
}
.filter-bar input, .filter-bar select {
    border: 1px solid #d9d9d9;
    color: #333;
    font-size: 13px;
    background-color: #fff;
}
.filter-bar input::placeholder { color: #999; }
.filter-toggle-btn {
    border: 1px solid #e0e0e0;
    border-radius: 999px;
    padding: 0.5rem 1.25rem;
    box-shadow: 0 8px 16px rgba(0,0,0,0.08);
}
.filter-toggle-btn .badge {
    font-size: 11px;
    border-radius: 999px;
}
.search-field {
    background-color: #fff;
    border: 1px solid #d9d9d9;
    border-radius: 6px;
    padding: 0 0.75rem;
}
.search-field input {
    border: none;
    background: transparent;
    flex: 1;
    padding-left: 0;
}
.search-field input:focus,
.filter-bar select:focus,
.filter-bar input:focus {
    box-shadow: none;
    border-color: #a0a0a0;
}
.filter-bar .form-select,
.filter-bar .form-control {
    border-radius: 6px;
}
#filterBar {
    transition: all 0.25s ease;
}
@media (max-width: 991.98px) {
    #filterBar {
        display: none;
        margin-top: 1rem;
    }
    #filterBar.open {
        display: block;
    }
}
</style>

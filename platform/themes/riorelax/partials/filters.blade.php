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
        'search'   => request('search'),
        'category' => request('category'),
        'trainer'  => request('trainer'),
        'sort'     => request('sort'),
    ])->filter(fn ($value) => filled($value));

    $resultCount = isset($courses)
        ? ($courses->total() ?? 0)
        : (isset($rooms) ? ($rooms->total() ?? 0) : \Theme\Riorelax\Supports\FilterHelper::count(request(), $type));

    $formattedCount = number_format($resultCount, 0, ',', '.');
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
        <form id="mainFilterForm" method="GET" action="{{ url()->current() }}" class="filter-form d-flex flex-wrap flex-lg-nowrap align-items-stretch gap-3">
            <input type="hidden" name="filter_type" value="{{ $type }}">

            <div class="filter-meta d-flex align-items-center gap-2 text-muted">
                <span class="filter-badge d-inline-flex align-items-center justify-content-center"><i class="fal fa-sliders-h"></i></span>
                <span class="fw-semibold">Filter</span>
            </div>

            {{-- 🔍 Suche --}}
            <div class="filter-field flex-grow-1">
                <label class="visually-hidden" for="filter-search">Suche</label>
                <div class="search-field d-flex align-items-center">
                    <i class="fal fa-search me-2 text-muted fs-5"></i>
                    <input id="filter-search" type="text" name="search" class="form-control form-control-sm"
                           placeholder="Suche {{ $isCourses ? 'nach Kurs oder Coach' : 'nach Raum' }}"
                           value="{{ request('search') }}">
                </div>
            </div>

            {{-- 🧭 Kategorie --}}
            <div class="filter-field">
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

            {{-- 👩‍🏫 Coach (nur bei Kursen) --}}
            @if($isCourses)
                <div class="filter-field">
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
            <div class="filter-field">
                <label class="visually-hidden" for="filter-sort">Sortieren</label>
                <select id="filter-sort" name="sort" class="form-select form-select-sm text-muted">
                    <option value="">Sortieren nach</option>
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Neueste</option>
                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Älteste</option>
                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Preis aufsteigend</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Preis absteigend</option>
                </select>
            </div>

        </form>
        @if($activeFilters->isNotEmpty())
            <div class="active-filter-chips d-flex flex-wrap gap-2 mt-3">
                @foreach($activeFilters as $key => $value)
                    @php
                        $labelMap = [
                            'search' => 'Suche',
                            'category' => $isCourses ? 'Kategorie' : 'Kategorie',
                            'trainer' => 'Coach',
                            'sort' => 'Sortierung',
                        ];
                        $sortMap = [
                            'newest' => 'Neueste',
                            'oldest' => 'Älteste',
                            'price_asc' => 'Preis aufsteigend',
                            'price_desc' => 'Preis absteigend',
                        ];

                        $label = $labelMap[$key] ?? ucfirst($key);
                        $display = is_array($value) ? implode(', ', $value) : $value;

                        if ($key === 'category') {
                            $display = optional($categories->firstWhere('id', (int) $value))->name ?? $display;
                        } elseif ($key === 'trainer') {
                            $display = optional($trainers->firstWhere('id', (int) $value))->name ?? $display;
                        } elseif ($key === 'sort') {
                            $display = $sortMap[$value] ?? $display;
                        }
                    @endphp
                    <button type="button" class="filter-chip badge bg-primary-subtle text-primary rounded-pill px-3 py-2 small d-inline-flex align-items-center gap-2" data-key="{{ $key }}">
                        <span>{{ $label }}: {{ $display }}</span>
                        <i class="fal fa-times"></i>
                    </button>
                @endforeach
            </div>
        @endif
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
  const triggerSubmit = () => {
    const searchInput = f.querySelector('input[name="search"]');
    if (searchInput) {
      const trimmed = searchInput.value.trim();
      if (trimmed !== searchInput.value) {
        searchInput.value = trimmed;
      }
    }

    if (typeof f.requestSubmit === 'function') {
      f.requestSubmit();
    } else {
      f.submit();
    }

    window.location.href = url;
  };

  f.querySelectorAll('select').forEach((el) => {
    el.addEventListener('change', triggerSubmit);
  });

  const searchField = f.querySelector('input[name="search"]');
  if (searchField) {
    searchField.addEventListener('keydown', (event) => {
      if (event.key === 'Enter') {
        event.preventDefault();
        applyFilters();
      }
    });
  }

  f.addEventListener('submit', (event) => {
    event.preventDefault();
    applyFilters();
  });

  document.addEventListener('click', (event) => {
    const chip = event.target.closest('.filter-chip');
    if(!chip) return;
    event.preventDefault();
    const key = chip.getAttribute('data-key');
    if(!key) return;
    const field = f.querySelector(`[name="${key}"]`);
    if(field){
      if(field.tagName === 'SELECT') {
        field.selectedIndex = 0;
      } else {
        field.value = '';
      }
    }
    if(key === 'sort'){
      const sortField = f.querySelector('#filter-sort');
      if(sortField) sortField.selectedIndex = 0;
    }
    applyFilters();
  });
})();
</script>

<style>
.filter-bar-wrapper, .filter-bar {
    font-size: 13px;
    color: #444;
}
.filter-bar {
    background-color: #F4F4F4;
    padding: 1.5rem;
    border: 1px solid #e0e0e0;
}
.filter-badge {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background-color: #fff;
    border: 1px solid #e0e0e0;
    color: #666;
    font-size: 16px;
}
.filter-bar input, .filter-bar select {
    border: 1px solid #d9d9d9;
    color: #333;
    font-size: 13px;
    background-color: #fff;
}
.filter-form {
    width: 100%;
}
.filter-form > * {
    flex: 0 0 auto;
}
.filter-form .filter-field {
    min-width: 180px;
}
.filter-form .filter-meta {
    flex: 0 0 auto;
    white-space: nowrap;
}
.filter-form .filter-field.flex-grow-1 {
    min-width: 220px;
}
.filter-bar input::placeholder { color: #999; }
.filter-toggle-btn {
    border: 1px solid #e0e0e0;
    border-radius: 999px;
    padding: 0.5rem 1.25rem;
    box-shadow: 0 8px 16px rgba(0,0,0,0.08);
}
.filter-toggle-btn i {
    font-size: 16px;
}
.filter-toggle-btn span {
    font-weight: 500;
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
.active-filter-chips .badge {
    background-color: rgba(75, 119, 190, 0.15) !important;
    color: #2a4c7c !important;
}
.filter-chip {
    border: none;
    cursor: pointer;
    transition: background-color 0.2s ease, color 0.2s ease;
}
.filter-chip:hover {
    background-color: rgba(75, 119, 190, 0.25) !important;
    color: #1d3658 !important;
}
.filter-chip i {
    font-size: 12px;
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
    .filter-form {
        flex-direction: column;
    }
    .filter-form > * {
        width: 100%;
    }
    .filter-form .filter-meta {
        justify-content: center;
    }
}
</style>

@php
use Carbon\Carbon;

$now = now();

/* Sessions ermitteln */
$upcoming = $course->sessions()->where('start_date', '>=', $now)->orderBy('start_date')->get();
$next = $upcoming->first();
$hasMultiple = $upcoming->count() > 1;

/* Datum */
$format = fn($s) => $s ? Carbon::parse($s->start_date)->format('d.m.Y H:i') . '–' . Carbon::parse($s->end_date)->format('H:i') : null;
$dateLabel = $hasMultiple ? __('Mehrere Termine') : ($next ? $format($next) : __('Kein Termin verfügbar'));

/* Ziel-URL */
$cartUrl = $hasMultiple ? $course->url : (Route::has('public.course.checkout') ? route('public.course.checkout', $course->id) : $course->url);
@endphp

<style>
.course-card {
  background:#fff;
  border-radius:8px;
  box-shadow:0 2px 6px rgba(0,0,0,0.05);
  overflow:hidden;
  transition:transform .2s ease, box-shadow .2s ease;
  cursor:pointer;
}
.course-card:hover {
  transform:translateY(-2px);
  box-shadow:0 4px 12px rgba(0,0,0,0.08);
}
.course-card .thumb img {
  width:100%;
  aspect-ratio:16/9;
  object-fit:cover;
  display:block;
}
.course-body {
  padding:16px 16px 10px;
  display:flex;
  flex-direction:column;
  gap:8px;
}
.course-title {
  font-size:14px;
  font-weight:500;
  color:#000;
  margin:0;
}
.course-desc {
  font-size:10px;
  line-height:1.5;
  color:#555;
  display:-webkit-box;
  -webkit-line-clamp:2;
  -webkit-box-orient:vertical;
  overflow:hidden;
  margin:0;
}
.course-meta {
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:8px;
  margin-top:4px;
}
.course-meta-left {
  display:flex;
  align-items:center;
  gap:8px;
}
.mtxt {
  background:#F3F3F3;
  color:#578E88;
  font-weight:500;
  border-radius:0;
  padding:8px 14px;
  font-size:13px;
  line-height:16px;
  display:inline-flex;
  align-items:center;
  gap:6px;
}
.mtxt.missed { color:#E74C3C; }
.btn-cart {
  background:#578E88;
  color:#fff;
  padding:8px 14px;
  border-radius:4px;
  display:inline-flex;
  align-items:center;
  justify-content:center;
  transition:background .2s ease;
}
.btn-cart:hover { background:#4b7c75; }
</style>

<div class="course-card" onclick="window.location='{{ $course->url }}'">
  <div class="thumb">
    <img src="{{ RvMedia::getImageUrl($course->thumbnail, 'medium') }}" alt="{{ $course->name }}">
  </div>

  <div class="course-body">
    <h4 class="course-title">{{ $course->name }}</h4>

    @if ($course->subtitle)
      <p class="course-subtitle" style="font-weight:600;color:#578E88;margin-bottom:2px;">
        {{ $course->subtitle }}
      </p>
    @endif

    @if ($course->description)
      <p class="course-desc">{!! BaseHelper::clean(Str::limit($course->description, 120)) !!}</p>
    @endif

    <div class="course-meta">
      <div class="course-meta-left">
        <span class="mtxt"><i class="fal fa-calendar-alt"></i>{{ $dateLabel }}</span>
        @if ($course->price)
          <span class="mtxt">{{ format_price($course->price) }}</span>
        @endif
      </div>
      <a href="{{ $cartUrl }}" class="btn-cart" onclick="event.stopPropagation()">
        <i class="fal fa-shopping-cart"></i>
      </a>
    </div>
  </div>
</div>

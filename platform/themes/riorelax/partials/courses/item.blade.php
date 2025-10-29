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

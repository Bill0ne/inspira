@php
use Carbon\Carbon;

$now = now();

/* === Sessions ermitteln === */
$upcoming = $course->sessions()
    ->where('start_date', '>=', $now)
    ->orderBy('start_date')
    ->get();

$next = $upcoming->first();
$hasMultiple = $upcoming->count() > 1;

/* === Datum === */
$format = fn($s) => $s
    ? Carbon::parse($s->start_date)->format('d.m.Y H:i') . '–' . Carbon::parse($s->end_date)->format('H:i')
    : null;

$dateLabel = $hasMultiple
    ? __('Mehrere Termine')
    : ($next ? $format($next) : __('Kein Termin verfügbar'));

/* === Ziel-URL === */
$cartUrl = $hasMultiple
    ? $course->url
    : (Route::has('public.course.checkout')
        ? route('public.course.checkout', $course->id)
        : $course->url);

/* === Fallback-Bild === */
$thumbnail = $course->thumbnail;
$image = $thumbnail
    ? RvMedia::getImageUrl($thumbnail, 'medium', false, RvMedia::getDefaultImage())
    : RvMedia::getImageUrl('default-course.jpg', 'medium', false, RvMedia::getDefaultImage());
@endphp


<div class="course-card" onclick="window.location='{{ $course->url }}'">
  {{-- === Bildbereich === --}}
  <div class="thumb">
    <img src="{{ $image }}" alt="{{ $course->name }}">
  </div>

  {{-- === Textbereich === --}}
  <div class="course-body">
    <h4 class="course-title">{{ strip_tags($course->name) }}</h4>

    @if ($course->subtitle)
      <p class="course-subtitle">{{ strip_tags($course->subtitle) }}</p>
    @endif

    @if ($course->description)
      <p class="course-desc">{!! BaseHelper::clean(strip_tags($course->description, '<br><em>')) !!}</p>
    @endif

    {{-- === Footer-Bereich (zweizeilig) === --}}
    <div class="course-meta">
      {{-- Zeile 1: Chips (Datum + Preis) --}}
      <div class="course-meta-left">
        <span class="mtxt">
          <i class="fal fa-calendar-alt"></i>{{ $dateLabel }}
        </span>
        @if ($course->price)
          <span class="mtxt">{{ format_price($course->price) }}</span>
        @endif
      </div>

      {{-- Zeile 2: CTA --}}
      <div class="course-meta-cta">
        <a href="{{ $cartUrl }}" class="btn-cart" onclick="event.stopPropagation()">
          <i class="fal fa-shopping-cart"></i>
        </a>
      </div>
    </div>
  </div>
</div>

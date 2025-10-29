@php
use Carbon\Carbon;

$margin = $margin ?? false;
$now = now();

/* === Sessions / Termine === */
$upcomingSessionsQuery = $course->sessions()
    ->where('start_date', '>=', $now)
    ->orderBy('start_date');

$upcomingSessions = $upcomingSessionsQuery->get();
$upcomingSessionsCount = $upcomingSessions->count();
$nextSession = $upcomingSessionsCount ? $upcomingSessions->first() : null;

$lastSession = $course->sessions()
    ->where('start_date', '<', $now)
    ->orderByDesc('start_date')
    ->first();

/* === Datumsformatierung === */
$formatSessionRange = static function (?object $session, string $dayFmt = 'd.m.Y', string $timeFmt = 'H:i'): ?string {
    if (!$session) return null;
    $s = Carbon::parse($session->start_date);
    $e = $session->end_date ? Carbon::parse($session->end_date) : null;
    if ($e) {
        return $s->isSameDay($e)
            ? $s->format("$dayFmt $timeFmt") . '–' . $e->format($timeFmt)
            : $s->format("$dayFmt $timeFmt") . ' – ' . $e->format("$dayFmt $timeFmt");
    }
    return $s->format("$dayFmt $timeFmt");
};

$dateDisplay = $formatSessionRange($nextSession);

/* === Date-Chip Logik === */
$dateChipLabel = null;
$dateChipClass = 'mtxt';
$dateChipTitle = null;

if ($upcomingSessionsCount > 1) {
    $dateChipLabel = __('Mehrere Termine');
    $dateChipTitle = $dateChipLabel;
} elseif ($nextSession) {
    $dateChipLabel = $dateDisplay;
    $dateChipTitle = $dateDisplay;
} elseif ($lastSession) {
    $recentThreshold = $now->copy()->subDays(10);
    $lastSessionDate = Carbon::parse($lastSession->start_date);
    if ($lastSessionDate->greaterThanOrEqualTo($recentThreshold)) {
        $dateChipLabel = __('Leider verpasst');
        $dateChipClass .= ' missed';
        $dateChipTitle = $dateChipLabel;
    }
}

if (is_null($dateChipLabel) && $upcomingSessionsCount === 0 && !$lastSession) {
    $dateChipLabel = __('Kein Termin verfügbar');
    $dateChipTitle = $dateChipLabel;
}

/* === Sitzplatz-Infos === */
$totalCapacity = 0;
$totalBooked = 0;
$hasUnlimited = false;

foreach ($upcomingSessions as $session) {
    if (is_null($session->available_seats)) {
        $hasUnlimited = true;
        break;
    }
    $bookedCount = $session->getBookedCount();
    $totalCapacity += $session->available_seats;
    $totalBooked += $bookedCount;
}

$hasAvailableSessions = $upcomingSessions->contains(fn($s) => $s->hasAvailableSeats());
$isSoldOut = !$hasUnlimited && !$hasAvailableSessions;
$percent = $totalCapacity > 0 ? (int) round(min(100, ($totalBooked / (float)$totalCapacity) * 100)) : null;

$showSeatChip = false;
$seatChipClass = '';

if (!is_null($percent) && $percent >= 30) {
    $showSeatChip  = true;
    $seatChipClass = 'seat-gray';
    if ($percent >= 60)  $seatChipClass = 'seat-orange';
    if ($percent >= 80)  $seatChipClass = 'seat-red';
}

/* === Button Ziel === */
$cartUrl = $upcomingSessionsCount > 1
    ? $course->url
    : (Route::has('public.course.checkout')
        ? route('public.course.checkout', $course->id)
        : $course->url);

@endphp

<style>
.course-card {
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.05);
  overflow: hidden;
  transition: all .25s ease;
  cursor: pointer;
}
.course-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 10px rgba(0,0,0,0.08);
}

.course-card .thumb img {
  width: 100%;
  height: auto;
  object-fit: cover;
  display: block;
}

.course-card-body {
  padding: 16px;
  display: flex;
  flex-direction: column;
  gap: 6px;
  min-height: 100%;
}

.course-card-body h4 {
  font-size: 16px;
  line-height: 20px;
  font-weight: 600;
  color: #000;
  margin-bottom: 4px;
}

.course-card-body .desc {
  font-size: 12px;
  line-height: 18px;
  color: #333;
  margin-bottom: 8px;
}

.course-meta-bottom {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
}

/* Chips übernehmen dein bestehendes Design */
.course-card .mtxt {
  background:#F3F3F3 !important;
  color:#578E88 !important;
  border-radius:0;
  padding:8px 14px;
  display:inline-flex;
  align-items:center;
  gap:7px;
  font-size:13px;
  font-weight:500;
  line-height:16px;
}
.course-card .mtxt.missed { color:#E74C3C !important; }

.course-card .btn-cart {
  background:#578E88;
  color:#fff;
  padding:8px 14px;
  border:none;
  border-radius:4px;
  display:inline-flex;
  align-items:center;
  justify-content:center;
  transition:background .2s ease;
}
.course-card .btn-cart:hover { background:#4b7c75; }
</style>

<div class="course-card" onclick="window.location='{{ $course->url }}'">
  <div class="thumb">
    <img src="{{ RvMedia::getImageUrl($course->thumbnail, 'medium') }}" alt="{{ $course->name }}">
  </div>

  <div class="course-card-body">

    {{-- Titel & Beschreibung --}}
    <h4>{{ $course->name }}</h4>
    @if ($description = $course->description)
      <p class="desc" title="{{ $description }}">
        {!! BaseHelper::clean(Str::limit($description, 120)) !!}
      </p>
    @endif

    {{-- Meta unten: Datum + Preis + Button --}}
    <div class="course-meta-bottom">
      <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
        @if ($dateChipLabel)
          <span class="{{ $dateChipClass }}" title="{{ $dateChipTitle }}">
            <i class="fal fa-calendar-alt me-1"></i> {{ $dateChipLabel }}
          </span>
        @endif
        @if ($course->price)
          <span class="mtxt">{{ format_price($course->price) }}</span>
        @endif
      </div>

      <a href="{{ $cartUrl }}" class="btn-cart" onclick="event.stopPropagation();">
        <i class="fal fa-shopping-cart"></i>
      </a>
    </div>

  </div>
</div>


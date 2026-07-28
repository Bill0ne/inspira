@php
    use Carbon\Carbon;
    Theme::set('pageTitle', $course->name);

    $now = now();

    // --- Upcoming sessions ---
    $upcomingSessionsQuery = $course->sessions()
        ->where('start_date', '>=', $now)
        ->orderBy('start_date');

    $upcomingSessions = $upcomingSessionsQuery->get();
    $upcomingSessionsCount = $upcomingSessions->count();
    $nextSession = $upcomingSessionsCount ? $upcomingSessions->first() : null;

    // --- Helper for formatting range ---
    $formatRange24h = static function (?string $start, ?string $end, string $dayFmt = 'd.m.Y', string $timeFmt = 'H:i'): ?string {
        if (!$start) return null;
        $s = Carbon::parse($start);
        $e = $end ? Carbon::parse($end) : null;

        if ($e) {
            return $s->isSameDay($e)
                ? $s->format("$dayFmt $timeFmt") . '–' . $e->format($timeFmt)
                : $s->format("$dayFmt $timeFmt") . ' – ' . $e->format("$dayFmt $timeFmt");
        }
        return $s->format("$dayFmt $timeFmt");
    };

    $dateDisplayForNext = $nextSession ? $formatRange24h($nextSession->start_date, $nextSession->end_date) : null;

    // === Seats/Progress Logic (same as card) ===
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
    $allSoldOut = !$hasUnlimited && !$hasAvailableSessions;

    $percent = $totalCapacity > 0
        ? (int) round(min(100, ($totalBooked / (float)$totalCapacity) * 100))
        : null;

    // --- Seat Chip ---
    // Auslastung erst ab 60% zeigen: darunter bleibt die Teilnehmerzahl versteckt,
    // ab 60% orange, ab 80% rot.
    $showSeatChip  = false;
    $seatChipClass = '';

    if (!is_null($percent)) {
        if ($percent >= 60) {
            $showSeatChip  = true;
            $seatChipClass = 'seat-orange';
            if ($percent >= 80)  $seatChipClass = 'seat-red';
        }
    }

    // --- Button Disable Logic ---
    $isSingleSoldOut = false;
    if (!$course->isRecurring()) {
        $first = $upcomingSessions->first();
        if ($first) {
            $isSingleSoldOut = !$first->hasAvailableSeats();
        }
    }
@endphp


<style>
/* ===== Nur das Nötigste – Typo bleibt wie im Original ===== */

/* Chips-Row (Dauer, Kategorie, Preis) – wie auf der Karte */
.course-detail-chips{
  display:flex; align-items:center; gap:8px; flex-wrap:wrap;
  margin:6px 0 12px 0;
}
.course-detail-chips .chip{
  background:#F3F3F3; color:#578E88; border-radius:0;
  padding:8px 14px;
  display:inline-flex; align-items:center; gap:7px;
  font-size:13px; font-weight:500; line-height:16px;
  text-decoration:none; border:0; box-shadow:none;
  white-space:nowrap;
}

/* Seat-Chip Farbvarianten (wie Karte) */
.course-detail-chips .chip.seat-gray{ background:#F3F3F3; color:#578E88; }
.course-detail-chips .chip.seat-orange{ background:#FFA500; color:#fff; }
.course-detail-chips .chip.seat-red{ background:#E74C3C; color:#fff; }

/* Select modernisieren (ohne Typo-Änderung am Titel/Body) */
.course-detail-form h4{ margin:8px 0 6px 0; }
.course-detail-select{
  width:100%;
  appearance:none; -webkit-appearance:none; -moz-appearance:none;
  border:1px solid #d1d5db; background:#fff;
  border-radius:6px; padding:10px 12px;
  outline:none; transition:border-color .15s ease, box-shadow .15s ease;
  background-image:none;
}
.course-detail-select:focus{
  border-color:#578E88;
  box-shadow:0 0 0 3px rgba(87,142,136,.15);
}

/* CTA 100% breit (Titel/Beschreibung bleiben unberührt) */
.course-detail-cta{ display:block; width:100%; }
.course-detail-cta.soldout{
  background:#D9D9D9 !important; color:#666 !important;
  cursor:not-allowed !important; pointer-events:none !important;
  border-color:#D9D9D9 !important;
}

/* Container leicht harmonisiert, ohne Schriftgrößen zu ändern */
.course-card-detail{ border-radius:10px; background:#fff; padding:16px; }
.course-card-detail .img-wrap{ margin-bottom:16px; }
.course-description{ margin-top:16px; } /* nur Abstand, keine Typo-Änderung */
</style>

<div class="course-detail-area pt-60 pb-60">
  <div class="container">
    <div class="row">
      <div class="col-lg-8 col-md-12">

        @if($course->thumbnail)
          <div class="img-wrap">
            <img src="{{ RvMedia::getImageUrl($course->thumbnail, 'large') }}"
                 alt="{{ $course->name }}" class="img-fluid rounded">
          </div>
        @endif

        <div class="course-card-detail shadow-sm mb-5 position-relative">
          <h2 class="mb-3">{{ $course->name }}</h2>
          <div class="course-detail-chips">
            @if($showSeatChip && !is_null($totalCapacity))
              <div class="chip {{ $seatChipClass }}" title="{{ $percent }}%">
                <i class="fal fa-user" aria-hidden="true"></i>
                  {{ $totalBooked }} / {{ $totalCapacity }}
              </div>
            @endif
            @if($dateDisplayForNext)
              <div class="chip" title="{{ $dateDisplayForNext }}">
                <i class="fal fa-calendar-alt" aria-hidden="true"></i>
                {{ $dateDisplayForNext }}
              </div>
            @endif

            @if($course->duration)
              <div class="chip" title="{{ __('Dauer') }}">
                {{ __('Dauer') }}: {{ $course->duration }}
              </div>
            @endif

            @if($course->category)
              <div class="chip" title="{{ $course->category->name }}">
                {{ $course->category->name }}
              </div>
            @endif
                @if($course->price)
                    @php
                        $pricing = course_price_breakdown($course, auth('customer')->user());
                        $hasDiscount = $pricing['has_discount'];
                    @endphp

                    <div class="chip price-chip d-flex align-items-center">
                        @if($hasDiscount)
                            <span class="old-price text-decoration-line-through text-muted me-2">
                {{ course_format_price($pricing['base_gross']) }}
            </span>
                            <span class="new-price text-success fw-bold">
                {{ course_format_price($pricing['calculated_gross']) }}
            </span>
                        @else
                            <span class="price fw-bold">{{ course_format_price($pricing['calculated_gross']) }}</span>
                        @endif
                    </div>

                    @if($hasDiscount && abs($pricing['discount_gross']) > 0)
                        <div class="chip discount-info text-success small mt-1">
                            <i class="fas fa-tag me-1"></i>
                            {{ __('You save :amount', ['amount' => course_format_price(abs($pricing['discount_gross']))]) }}
                        </div>
                    @endif
                @endif

          </div>
          @if($upcomingSessionsCount > 0)
            @if($course->isRecurring() || $upcomingSessionsCount > 1)
              @php
                  $disableRecurringCta = $allSoldOut;
              @endphp
              <div class="course-detail-form">
                <h4>{{ __('Termin wählen') }}</h4>

                <form action="{{ route('public.course.booking') }}" method="POST" class="mb-3">
                  @csrf
                  <input type="hidden" name="course_id" value="{{ $course->id }}">

                  <select name="session_id" class="course-detail-select mb-2" required {{ $disableRecurringCta ? 'disabled' : '' }}>
                    @foreach($upcomingSessions as $session)
                      @php
                        $label = $formatRange24h($session->start_date, $session->end_date);
                        $cap   = $session->available_seats;
                        $book  = (int) $session->getBookedCount();
                        $full  = (!is_null($cap) && (int)$cap > 0 && $book >= (int)$cap);
                      @endphp
                      <option value="{{ $session->id }}" {{ $full ? 'disabled' : '' }}>
                        {{ $label }} {{ $full ? __('– Ausgebucht') : '' }}
                      </option>
                    @endforeach
                  </select>
                  <button type="submit"
                          class="btn btn-primary btn-lg course-detail-cta {{ $disableRecurringCta ? 'soldout' : '' }}"
                          aria-disabled="{{ $disableRecurringCta ? 'true' : 'false' }}">
                    {{ $disableRecurringCta ? __('Ausgebucht') : __('Jetzt Buchen') }}
                  </button>
                </form>
              </div>
            @else
              @php
                $first = $upcomingSessions->first();
                $singleLabel = $first ? $formatRange24h($first->start_date, $first->end_date) : null;
                $disableSingleCta = $isSingleSoldOut;
              @endphp
              @if($singleLabel)
                <div class="mb-2"><strong>{{ __('Termin') }}:</strong> {{ $singleLabel }}</div>
              @endif
              <form action="{{ route('public.course.booking') }}" method="POST" class="mb-3">
                @csrf
                <input type="hidden" name="course_id" value="{{ $course->id }}">
                @if($first)
                  <input type="hidden" name="session_id" value="{{ $first->id }}">
                @endif
                <button type="submit"
                        class="btn btn-primary btn-lg course-detail-cta {{ $disableSingleCta ? 'soldout' : '' }}"
                        aria-disabled="{{ $disableSingleCta ? 'true' : 'false' }}"
                        {{ $disableSingleCta ? 'disabled' : '' }}>
                  {{ $disableSingleCta ? __('Ausgebucht') : __('Jetzt Buchen') }}
                </button>
              </form>
            @endif
          @endif
          <div class="course-description">
            {!! BaseHelper::clean($course->description) !!}
          </div>
        </div>

        @if(\Botble\Courses\Facades\CourseHelper::isReviewEnabled())
          @include(Theme::getThemeNamespace('views.courses.partials.reviews'), ['model' => $course])
        @endif
      </div>
      <div class="col-lg-4 col-md-12">
        @if($course->instructor)
          <div class="instructor-box shadow-sm p-4 mb-5" style="border-radius:10px; background:#fff;">
            <div class="d-flex align-items-center mb-3">
              @if($course->instructor->photo)
                <img src="{{ RvMedia::getImageUrl($course->instructor->photo, 'thumb') }}"
                     alt="{{ $course->instructor->name }}"
                     class="rounded-circle me-3" width="80" height="80">
              @endif
              <div>
                <h5 class="mb-1">{{ $course->instructor->name ?? __('No Name') }}</h5>
                @if($course->instructor->email)
                  <p class="mb-1"><i class="fa fa-envelope"></i> {{ $course->instructor->email }}</p>
                @endif
                @if($course->instructor->phone)
                  <p class="mb-1"><i class="fa fa-phone"></i> {{ $course->instructor->phone }}</p>
                @endif
              </div>
            </div>
            @if($course->instructor->bio)
              <div>{!! BaseHelper::clean($course->instructor->bio) !!}</div>
            @endif
          </div>
        @endif
      </div>
    </div>

    {{-- Related Courses --}}
    @if($relatedCourses->isNotEmpty())
      <div class="related-courses mt-5">
        <h3 class="mb-4">{{ __('Related Courses') }}</h3>
        <div class="row g-4">
          @foreach($relatedCourses as $related)
            <div class="col-12 col-sm-6 col-lg-3">
              {!! Theme::partial('courses.item', ['course' => $related]) !!}
            </div>
          @endforeach
        </div>
      </div>
    @endif
  </div>
</div>

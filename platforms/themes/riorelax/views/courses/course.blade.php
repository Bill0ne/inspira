@php
    use Carbon\Carbon;
    Theme::set('pageTitle', $course->name);

    // 24h-Format mit "kein doppeltes Datum am selben Tag"
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

    // ---- Seats/Progress-Logik (Detailseite) ----
    // Nächste "relevante" Session bestimmen: bei recurring erste zukünftige, sonst erste
    $nextSession = $course->isRecurring()
        ? $course->sessions()->where('start_date', '>=', now())->orderBy('start_date')->first()
        : $course->sessions()->orderBy('start_date')->first();

    $dateDisplayForNext = $nextSession ? $formatRange24h($nextSession->start_date, $nextSession->end_date) : null;

    // Kapazität + Buchungen der nächsten Session
    $capacityNext = $nextSession?->available_seats;                   // null = unlimited
    $bookedNext   = $nextSession ? (int) $nextSession->bookings()->count() : 0;

    // Prozent nur, wenn Kapazität gesetzt ist
    $percentNext  = (!is_null($capacityNext) && (int)$capacityNext > 0)
        ? (int) round(min(100, ($bookedNext / (float)$capacityNext) * 100))
        : null;

    // Chip-Regeln (wie auf der Karte)
    $showSeatChip  = false;
    $seatChipClass = ''; // seat-gray | seat-orange | seat-red
    if (!is_null($percentNext)) {
        if ($percentNext >= 30) {
            $showSeatChip  = true;
            $seatChipClass = 'seat-gray';
            if ($percentNext >= 60) $seatChipClass = 'seat-orange';
            if ($percentNext >= 80) $seatChipClass = 'seat-red';
        }
    }

    // Button-Status: Single-Session voll? Recurring: alle voll?
    $isSingleSoldOut = false;
    $allSoldOut = false;

    if ($course->isRecurring()) {
        // Wenn ALLE Sessions voll -> Button disable + "Ausgebucht"
        $allSoldOut = $course->sessions->count() > 0
            ? $course->sessions->every(fn($s) => method_exists($s, 'hasAvailableSeats') ? !$s->hasAvailableSeats() : ($s->available_seats !== null && (int)$s->bookings()->count() >= (int)$s->available_seats))
            : false;
    } else {
        // Single: nur die erste/alleinige Session bewerten
        $first = $course->sessions->first();
        if ($first) {
            $cap = $first->available_seats;
            $isSingleSoldOut = (!is_null($cap) && (int)$cap > 0 && (int)$first->bookings()->count() >= (int)$cap);
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
      {{-- Left: Course Details --}}
      <div class="col-lg-8 col-md-12">

        {{-- Thumbnail --}}
        @if($course->thumbnail)
          <div class="img-wrap">
            <img src="{{ RvMedia::getImageUrl($course->thumbnail, 'large') }}"
                 alt="{{ $course->name }}" class="img-fluid rounded">
          </div>
        @endif

        {{-- Detail-Card --}}
        <div class="course-card-detail shadow-sm mb-5 position-relative">

          {{-- 1) Titel --}}
          <h2 class="mb-3">{{ $course->name }}</h2>

          {{-- 2) Chips: Seat-Chip (falls >=30%), Datum (nächste Session, falls vorhanden), Dauer, Kategorie, Preis --}}
          <div class="course-detail-chips">

            {{-- Seat-Chip vor dem Datum --}}
            @if($showSeatChip && !is_null($capacityNext))
              <div class="chip {{ $seatChipClass }}" title="{{ $percentNext }}%">
                <i class="fal fa-user" aria-hidden="true"></i>
                {{ $bookedNext }} / {{ $capacityNext }}
              </div>
            @endif

            {{-- Datum (nächste relevante Session) --}}
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
              <div class="chip" title="{{ format_price($course->price) }}">
                {{ format_price($course->price) }}
              </div>
            @endif
          </div>

          {{-- 3) Select Datum (24h) / CTA --}}
          @if($course->sessions->count() > 0)
            @if($course->isRecurring())
              @php
                  // Button disabled, wenn ALLE Sessions ausgebucht sind
                  $disableRecurringCta = $allSoldOut;
              @endphp
              <div class="course-detail-form">
                <h4>{{ __('Termin wählen') }}</h4>

                <form action="{{ route('public.course.booking') }}" method="POST" class="mb-3">
                  @csrf
                  <input type="hidden" name="course_id" value="{{ $course->id }}">

                  <select name="session_id" class="course-detail-select mb-2" required {{ $disableRecurringCta ? 'disabled' : '' }}>
                    @foreach($course->sessions as $session)
                      @php
                        $label = $formatRange24h($session->start_date, $session->end_date);
                        $cap   = $session->available_seats;
                        $book  = (int) $session->bookings()->count();
                        $full  = (!is_null($cap) && (int)$cap > 0 && $book >= (int)$cap);
                      @endphp
                      <option value="{{ $session->id }}" {{ $full ? 'disabled' : '' }}>
                        {{ $label }} {{ $full ? __('– Ausgebucht') : '' }}
                      </option>
                    @endforeach
                  </select>

                  {{-- CTA: 100% breit; bei "alle ausgebucht" -> grau + Text ändern --}}
                  <button type="submit"
                          class="btn btn-primary btn-lg course-detail-cta {{ $disableRecurringCta ? 'soldout' : '' }}"
                          aria-disabled="{{ $disableRecurringCta ? 'true' : 'false' }}">
                    {{ $disableRecurringCta ? __('Ausgebucht') : __('Jetzt Buchen') }}
                  </button>
                </form>
              </div>
            @else
              @php
                $first = $course->sessions->first();
                $singleLabel = $first ? $formatRange24h($first->start_date, $first->end_date) : null;
                $disableSingleCta = $isSingleSoldOut;
              @endphp

              {{-- Fixes Datum (24h) --}}
              @if($singleLabel)
                <div class="mb-2"><strong>{{ __('Termin') }}:</strong> {{ $singleLabel }}</div>
              @endif

              {{-- CTA: 100% breit; bei 100% voll -> grau + Text "Ausgebucht" --}}
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

          {{-- 5) Beschreibung (unverändert in Typo) --}}
          <div class="course-description">
            {!! BaseHelper::clean($course->description) !!}
          </div>
        </div>

        @if(\Botble\Courses\Facades\CourseHelper::isReviewEnabled())
          @include(Theme::getThemeNamespace('views.courses.partials.reviews'), ['model' => $course])
        @endif
      </div>

      {{-- Right: Instructor Info (unverändert) --}}
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
        <div class="row">
          @foreach($relatedCourses as $related)
            <div class="col-md-6 mb-3">
              {!! Theme::partial('courses.item', ['course' => $related]) !!}
            </div>
          @endforeach
        </div>
      </div>
    @endif
  </div>
</div>

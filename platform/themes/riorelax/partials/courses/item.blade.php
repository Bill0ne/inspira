@php
    use Carbon\Carbon;

    $margin = $margin ?? false;
    $now = now();

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

    $formatSessionRange = static function (?object $session, string $dayFmt = 'd.m.Y', string $timeFmt = 'H:i'): ?string {
        if (!$session) {
            return null;
        }

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

    $percent = $totalCapacity > 0
        ? (int) round(min(100, ($totalBooked / (float) $totalCapacity) * 100))
        : null;

    $showSeatChip = false;
    $seatChipClass = '';

    if (!is_null($percent)) {
        if ($percent >= 30) {
            $showSeatChip = true;
            $seatChipClass = 'seat-gray';
            if ($percent >= 60) {
                $seatChipClass = 'seat-orange';
            }
            if ($percent >= 80) {
                $seatChipClass = 'seat-red';
            }
        }
    }
@endphp

@once
    @push('styles')
        <style>
        .course-card.single-services {
            background: #ffffff;
            border-radius: 24px;
            overflow: hidden;
            border: 1px solid rgba(148, 163, 184, 0.25);
            transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .course-card.single-services:hover {
            transform: translateY(-6px);
            box-shadow: 0 30px 60px rgba(15, 23, 42, 0.12);
            border-color: rgba(124, 58, 237, 0.35);
        }

        .course-card .services-thumb {
            position: relative;
            overflow: hidden;
            aspect-ratio: 4 / 3;
        }

        .course-card .services-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform .4s ease;
        }

        .course-card:hover .services-thumb img {
            transform: scale(1.05);
        }

        .course-card .services-content {
            flex: 1 1 auto;
            display: flex;
            flex-direction: column;
            gap: 14px;
            padding: 1.5rem 1.6rem 1.8rem;
        }

        .course-card h4 {
            font-size: 1.05rem;
            font-weight: 600;
            margin: 0;
        }

        .course-card h4 a {
            color: #111827;
            text-decoration: none;
        }

        .course-card h4 a:hover {
            color: #7c3aed;
        }

        .course-card .room-item-custom-truncate {
            margin: 0;
            color: #4b5563;
            font-size: .925rem;
            line-height: 1.5;
        }

        .course-card__meta {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
        }

        .course-card__chip {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            background: #f3f4f6;
            color: #475569;
            padding: .45rem .75rem;
            border-radius: 999px;
            font-size: .75rem;
            font-weight: 600;
            line-height: 1;
        }

        .course-card__chip i {
            font-size: .85em;
        }

        .course-card__chip.missed {
            color: #dc2626;
            background: rgba(220, 38, 38, 0.12);
        }

        .course-card__chip.seat-gray {
            background: rgba(59, 130, 246, 0.12);
            color: #1d4ed8;
        }

        .course-card__chip.seat-orange {
            background: rgba(245, 158, 11, 0.15);
            color: #b45309;
        }

        .course-card__chip.seat-red {
            background: rgba(220, 38, 38, 0.15);
            color: #b91c1c;
        }

        .course-card__footer {
            margin-top: auto;
            display: flex;
            flex-direction: column;
            gap: .65rem;
        }

        .course-card__cta {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            padding: .85rem 1.4rem;
            border-radius: 14px;
            font-weight: 600;
            text-decoration: none;
        }

        .course-card__cta.soldout {
            background: #e5e7eb !important;
            color: #6b7280 !important;
            border-color: #e5e7eb !important;
            cursor: not-allowed !important;
        }

        .course-card__link {
            text-align: center;
            font-size: .85rem;
            font-weight: 500;
            color: #6366f1;
            text-decoration: none;
        }

        .course-card__link:hover {
            color: #4f46e5;
            text-decoration: underline;
        }

        @media (max-width: 767.98px) {
            .course-card .services-content {
                padding: 1.35rem 1.4rem 1.6rem;
            }
        }
        </style>
    @endpush
@endonce

<div @class(['single-services shadow-block mb-30 course-card h-100', 'ser-m' => !$margin])>
    <div class="services-thumb hover-zoomin">
        <a href="{{ $course->url }}">
            <img src="{{ RvMedia::getImageUrl($course->thumbnail, 'medium') }}" alt="{{ $course->name }}">
        </a>
    </div>

    <div class="services-content">
        <h4><a href="{{ $course->url }}">{{ $course->name }}</a></h4>

        @if ($description = $course->description)
            <p class="room-item-custom-truncate" title="{{ $description }}">
                {!! BaseHelper::clean(Str::limit($description, 120)) !!}
            </p>
        @endif

        <div class="course-card__meta">
            @if ($showSeatChip)
                <span class="course-card__chip {{ $seatChipClass }}" title="{{ $percent }}%">
                    <i class="fal fa-user" aria-hidden="true"></i>
                    {{ $totalBooked }} / {{ $totalCapacity }}
                </span>
            @endif

            @if ($dateChipLabel)
                <span class="course-card__chip {{ $dateChipClass }}" title="{{ $dateChipTitle }}">
                    <i class="fal fa-calendar-alt" aria-hidden="true"></i>
                    {{ $dateChipLabel }}
                </span>
            @endif

            @if ($course->price)
                <span class="course-card__chip">
                    <i class="fal fa-euro-sign" aria-hidden="true"></i>
                    {{ format_price($course->price) }}
                </span>
            @endif
        </div>

        <div class="course-card__footer">
            <a href="{{ $course->url }}"
               class="course-card__cta ss-btn {{ $isSoldOut ? 'soldout' : '' }}"
               aria-disabled="{{ $isSoldOut ? 'true' : 'false' }}"
               data-animation="fadeInRight" data-delay=".8s">
                {{ $isSoldOut ? __('Ausgebucht') : __('Jetzt Buchen') }}
            </a>

            <a class="course-card__link" href="{{ $course->url }}" aria-label="Mehr Infos zu {{ $course->name }}">
                {{ __('Mehr Infos') }}
            </a>
        </div>
    </div>
</div>

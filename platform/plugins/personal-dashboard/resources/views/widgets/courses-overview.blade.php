@php
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

/* === Gewichtungen fürs Kurs-Ranking === */
$weightBookings  = 0.5;
$weightRevenue   = 0.3;
$weightOccupancy = 0.2;

/* =========================================
 |  TOP KURSE (mit Umsatz aus payments)
 |========================================= */
$courses = DB::table('courses')
    ->leftJoin('course_bookings', 'courses.id', '=', 'course_bookings.course_id')
    ->leftJoin('payments', function ($join) {
        $join->on('payments.order_id', '=', 'course_bookings.id');
    })
    ->select(
        'courses.id',
        'courses.name',
        'courses.thumbnail',
        'courses.price',
        'courses.number_of_seats',
        'courses.start_date',
        'courses.end_date',
        DB::raw('COUNT(DISTINCT course_bookings.id) as bookings_count'),
        // Umsatz nur aus bezahlten Payments
        DB::raw("SUM(CASE WHEN payments.status IN ('paid','completed','success') THEN payments.amount ELSE 0 END) as revenue_sum")
    )
    ->groupBy(
        'courses.id', 'courses.name', 'courses.thumbnail', 'courses.price',
        'courses.number_of_seats', 'courses.start_date', 'courses.end_date'
    )
    ->get();

// Score berechnen
foreach ($courses as $c) {
    $bookings   = (int) ($c->bookings_count ?? 0);
    $revenue    = (float) ($c->revenue_sum ?? 0.0);
    $capacity   = max((int) ($c->number_of_seats ?? 0), 1);
    $occupancy  = min($bookings / $capacity, 1); // 0..1

    $c->score = ($bookings * $weightBookings)
              + (($revenue * $weightRevenue) / 100)
              + (($occupancy * 100) * $weightOccupancy);
}
$topCourses = collect($courses)->sortByDesc('score')->take(5);

/* =========================================
 |  TOP COMMUNITY MITGLIEDER (Clients)
 |  – auf Basis realer Tabellen ht_customers,
 |    course_bookings, course_reviews
 |========================================= */
$members = DB::table('ht_customers')
    ->select('ht_customers.id','ht_customers.first_name','ht_customers.last_name','ht_customers.avatar','ht_customers.created_at','ht_customers.updated_at')
    ->get();

$now = now();
foreach ($members as $m) {
    $bookingsCount = DB::table('course_bookings')->where('customer_id', $m->id)->count();

    // Durchschnitts-Bewertung aus course_reviews (falls Einträge vorhanden)
    $avgStars = DB::table('course_reviews')->where('customer_id', $m->id)->avg('star');
    $avgStars = $avgStars ? (float)$avgStars : 0.0;

    // „Aktivität“: gab es in den letzten 30 Tagen eine Buchung?
    $recentBookingAt = DB::table('course_bookings')->where('customer_id', $m->id)->max('created_at');
    $recentDays = $recentBookingAt ? $now->diffInDays(\Carbon\Carbon::parse($recentBookingAt)) : 999;

    // simpler Aktivitätsfaktor (1 = aktiv, 0 = inaktiv)
    $recentActivityFactor = $recentDays <= 30 ? 1 : 0;

    // Score (Client-Logik)
    $m->score = ($bookingsCount * 0.6) + ($avgStars * 0.3) + ($recentActivityFactor * 0.1);
    $m->label = 'Top Client';

    // Aktivitäts-Punkte (max 5) – grün (≤10 Tage), orange (≤20), rot (>20), Rest grau
    $dots = '';
    for ($i = 1; $i <= 5; $i++) {
        if ($i === 1) {
            if ($recentDays <= 10)      $color = '#47B881'; // grün
            elseif ($recentDays <= 20)  $color = '#F0AD4E'; // orange
            else                         $color = '#E74C3C'; // rot
        } else {
            $color = '#CCC'; // grau (Platzhalter)
        }
        $dots .= "<span style='display:inline-block;width:8px;height:8px;border-radius:50%;background:$color;margin-right:2px;'></span>";
    }
    $m->activity_dots = $dots;
}
$topMembers = collect($members)->sortByDesc('score')->take(5);

/* =========================================
 |  LETZTE BUCHUNGEN (5)
 |  – zeigt Preis aus course_bookings.amount
 |  – Status aus course_bookings.status
 |  – User aus ht_customers
 |  – Auslastung = Buchungen des Kurses / number_of_seats
 |========================================= */
// Für die Auslastung brauchen wir pro Buchungs-Zeile die Kurs-Buchungsanzahl:
$bookings = DB::table('course_bookings')
    ->join('courses', 'course_bookings.course_id', '=', 'courses.id')
    ->join('ht_customers', 'course_bookings.customer_id', '=', 'ht_customers.id')
    ->leftJoin(DB::raw('(SELECT course_id, COUNT(*) as cnt FROM course_bookings GROUP BY course_id) as cb_cnt'), 'cb_cnt.course_id', '=', 'courses.id')
    ->select(
        'course_bookings.id as booking_id',
        'course_bookings.status as booking_status',
        'course_bookings.amount as booking_amount',
        'course_bookings.created_at as booked_at',
        'courses.id as course_id',
        'courses.name as course_name',
        'courses.number_of_seats',
        DB::raw('COALESCE(cb_cnt.cnt, 0) as course_booked_count'),
        'ht_customers.first_name',
        'ht_customers.last_name',
        'ht_customers.avatar'
    )
    ->orderByDesc('course_bookings.created_at')
    ->limit(5)
    ->get();
@endphp

<div class="card border-0 shadow-sm w-100" style="background:#f9f9f9; border-radius:10px;">
  <div class="card-body p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="fw-semibold mb-0">Kurse Übersicht</h5>
      <span class="text-muted small">Today ▾</span>
    </div>

    <div class="row g-3">
      <!-- =================== Top Kurse =================== -->
      <div class="col-md-4">
        <div class="p-3 bg-white rounded shadow-sm h-100">
          <h6 class="fw-semibold mb-3">Top Kurse</h6>

          @foreach($topCourses as $c)
            @php
              $booked   = (int) $c->bookings_count;
              $capacity = max((int) $c->number_of_seats, 1);
              $progress = min(100, round(($booked / $capacity) * 100));
              $filled   = (int) round($progress / 10); // 10 Stühle
              $chairs   = '';
              for ($i = 1; $i <= 10; $i++) {
                  $chairs .= $i <= $filled ? '🪑' : '<span style="opacity:0.2;">🪑</span>';
              }
              $img = $c->thumbnail
                    ? RvMedia::getImageUrl($c->thumbnail, 'thumb', false, RvMedia::getDefaultImage())
                    : RvMedia::getDefaultImage();
              $dateStr = $c->start_date ? \Carbon\Carbon::parse($c->start_date)->format('d.m.y') : '';
              $timeStr = $c->start_date && $c->end_date
                        ? (\Carbon\Carbon::parse($c->start_date)->format('H:i') . ' – ' . \Carbon\Carbon::parse($c->end_date)->format('H:i'))
                        : '';
            @endphp

            <div class="d-flex align-items-center justify-content-between mb-2 p-2 rounded" style="cursor:default;">
              <div class="d-flex align-items-center">
                <img src="{{ $img }}" style="width:40px;height:40px;border-radius:6px;object-fit:cover;margin-right:10px;">
                <div>
                  <div style="font-size:14px;font-weight:500;">{{ Str::limit($c->name, 40) }}</div>
                  <div style="font-size:12px;color:#666;">
                    {{ $dateStr }} {{ $timeStr ? $timeStr : '' }}
                  </div>
                </div>
              </div>
              <div class="text-end" style="font-size:12px;">
                {!! $chairs !!} <span class="text-muted">{{ $booked }}/{{ $capacity }}</span>
              </div>
            </div>
          @endforeach
        </div>
      </div>

      <!-- ============ Top Community Mitglieder ============ -->
      <div class="col-md-4">
        <div class="p-3 bg-white rounded shadow-sm h-100">
          <h6 class="fw-semibold mb-3">Top Community Mitglieder</h6>

          @foreach($topMembers as $m)
            @php
              $avatar = $m->avatar
                      ? RvMedia::getImageUrl($m->avatar, 'thumb', false, RvMedia::getDefaultImage())
                      : RvMedia::getDefaultImage();
            @endphp

            <div class="d-flex justify-content-between align-items-center mb-3 p-2 rounded">
              <div class="d-flex align-items-center">
                <img src="{{ $avatar }}" style="width:40px;height:40px;border-radius:50%;object-fit:cover;margin-right:10px;">
                <div>
                  <div class="fw-semibold" style="font-size:14px;">{{ $m->first_name }} {{ $m->last_name }}</div>
                  <div style="font-size:12px;color:#777;">
                    {!! $m->activity_dots !!} &nbsp; {{ $m->label }}
                  </div>
                </div>
              </div>
              <div class="text-end">
                <span class="badge text-bg-light" style="font-size:12px;">Score {{ round($m->score) }}</span>
              </div>
            </div>
          @endforeach
        </div>
      </div>

      <!-- =================== Letzte Buchungen =================== -->
      <div class="col-md-4">
        <div class="p-3 bg-white rounded shadow-sm h-100">
          <h6 class="fw-semibold mb-3">Letzte Buchungen</h6>

          @foreach($bookings as $b)
            @php
              $booked   = (int) $b->course_booked_count;
              $capacity = max((int) $b->number_of_seats, 1);
              $progress = min(100, round(($booked / $capacity) * 100));
              $filled   = (int) round($progress / 10);
              $chairs   = '';
              for ($i=1; $i<=10; $i++) {
                  $chairs .= $i <= $filled ? '🪑' : '<span style="opacity:0.2;">🪑</span>';
              }
              $avatar = $b->avatar
                      ? RvMedia::getImageUrl($b->avatar, 'thumb', false, RvMedia::getDefaultImage())
                      : RvMedia::getDefaultImage();
              $url = URL::to('/admin/course-bookings/edit/' . $b->booking_id);
              $dateTime = \Carbon\Carbon::parse($b->booked_at)->format('d.m.Y H:i');
              $isPaid = in_array(strtolower($b->booking_status), ['paid', 'completed', 'success']);
            @endphp

            <div onclick="window.location='{{ $url }}'"
                 style="cursor:pointer;background:#fff;border-radius:8px;padding:8px 10px;margin-bottom:8px;border:1px solid #eee;">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <div style="font-size:13px;">{{ Str::limit($b->course_name,40) }}</div>
                <div style="font-size:11px;">{!! $chairs !!} <span class="text-muted">{{ $booked }}/{{ $capacity }}</span></div>
              </div>

              <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                  <img src="{{ $avatar }}" style="width:30px;height:30px;border-radius:50%;object-fit:cover;margin-right:8px;">
                  <div style="font-size:12px;">{{ $b->first_name }} {{ $b->last_name }}</div>
                </div>

                <div class="text-end" style="font-size:12px;">
                  {{ number_format((float)$b->booking_amount, 2, ',', '.') }} €
                  @if($isPaid)
                    <span style="background:#A9E6B3;color:#145C2E;font-weight:600;font-size:11px;border-radius:6px;padding:2px 6px;">Bezahlt</span>
                  @else
                    <span style="background:#FFDAD6;color:#A63C2D;font-weight:600;font-size:11px;border-radius:6px;padding:2px 6px;">Offen</span>
                  @endif
                  <div class="text-muted" style="font-size:11px;">{{ $dateTime }}</div>
                </div>
              </div>
            </div>
          @endforeach

        </div>
      </div>
    </div>
  </div>
</div>

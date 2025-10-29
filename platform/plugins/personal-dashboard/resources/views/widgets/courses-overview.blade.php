@php
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Carbon\Carbon;

/* === Gewichtungen für Kurs-Ranking === */
$weightBookings = 0.5;
$weightRevenue = 0.3;
$weightOccupancy = 0.2;

/* === TOP KURSE === */
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
        DB::raw("SUM(CASE WHEN payments.status IN ('paid','completed','success') THEN payments.amount ELSE 0 END) as revenue_sum")
    )
    ->groupBy('courses.id', 'courses.name', 'courses.thumbnail', 'courses.price', 'courses.number_of_seats', 'courses.start_date', 'courses.end_date')
    ->get();

foreach ($courses as $c) {
    $bookings = (int) ($c->bookings_count ?? 0);
    $revenue = (float) ($c->revenue_sum ?? 0.0);
    $capacity = max((int) ($c->number_of_seats ?? 0), 1);
    $occupancy = min($bookings / $capacity, 1);
    $c->score = ($bookings * $weightBookings)
              + (($revenue * $weightRevenue) / 100)
              + (($occupancy * 100) * $weightOccupancy);
}
$topCourses = collect($courses)->sortByDesc('score')->take(5);

/* === TOP COMMUNITY === */
$clients = DB::table('ht_customers')->get();
foreach ($clients as $m) {
    $bookingsCount = DB::table('course_bookings')->where('customer_id', $m->id)->count();
    $avgStars = DB::table('course_reviews')->where('customer_id', $m->id)->avg('star') ?? 0;
    $recentBookingAt = DB::table('course_bookings')->where('customer_id', $m->id)->max('created_at');
    $recentDays = $recentBookingAt ? now()->diffInDays(Carbon::parse($recentBookingAt)) : 999;
    $m->score = ($bookingsCount * 0.6) + ($avgStars * 0.3);
    $m->label = 'Top Client';
    $m->bookings = $bookingsCount;
    $dots = '';
    for ($i = 1; $i <= 5; $i++) {
        $color = '#CCC';
        if ($i == 1) {
            if ($recentDays <= 10) $color = '#47B881';
            elseif ($recentDays <= 20) $color = '#F0AD4E';
            else $color = '#E74C3C';
        }
        $dots .= "<span class='activity-dot' data-days='$recentDays' style='background:$color;'></span>";
    }
    $m->activity_dots = $dots;
}
$topMembers = collect($clients)->sortByDesc('score')->take(5);

/* === LETZTE BUCHUNGEN (nur vollständig bezahlte) === */
$bookings = DB::table('course_bookings')
    ->join('courses', 'course_bookings.course_id', '=', 'courses.id')
    ->join('ht_customers', 'course_bookings.customer_id', '=', 'ht_customers.id')
    ->leftJoin('payments', function ($join) {
        $join->on('payments.order_id', '=', 'course_bookings.id');
    })
    ->select(
        'course_bookings.id as booking_id',
        'course_bookings.status as booking_status',
        'course_bookings.amount as booking_amount',
        'course_bookings.created_at as booking_created',
        'payments.status as payment_status',
        'payments.amount as payment_amount',
        'payments.created_at as payment_created',
        'courses.name as course_name',
        'courses.id as course_id',
        'courses.number_of_seats',
        DB::raw('(SELECT COUNT(*) FROM course_bookings cb2 WHERE cb2.course_id = courses.id) as booked_count'),
        'ht_customers.first_name',
        'ht_customers.last_name',
        'ht_customers.avatar'
    )
    // ✅ Nur Buchungen mit abgeschlossener Zahlung
    ->whereIn('payments.status', ['paid', 'completed', 'success'])
    ->orderByDesc('payments.created_at')
    ->limit(5)
    ->get()
    ->map(function ($b) {
        // Einheitlicher Status für dein Blade-Tag
        $b->status = strtolower($b->payment_status ?? $b->booking_status ?? 'pending');
        // Betrag bevorzugt aus Payment
        $b->amount = $b->payment_amount ?? $b->booking_amount ?? 0;
        return $b;
    });


/* === Fallback-SVGs === */
$chairSvg = file_exists(public_path('images/icons/chair.svg'))
    ? file_get_contents(public_path('images/icons/chair.svg'))
    : '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><rect x="8" y="28" width="48" height="8" rx="2" fill="currentColor"/><rect x="12" y="8" width="40" height="18" rx="4" fill="currentColor"/><rect x="8" y="40" width="8" height="16" fill="currentColor"/><rect x="48" y="40" width="8" height="16" fill="currentColor"/></svg>';

$defaultAvatarSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><circle cx="32" cy="24" r="14" fill="#DDD"/><path d="M8 56c0-10.7 8.3-19 24-19s24 8.3 24 19" fill="#DDD"/></svg>';
@endphp

<style>
.dashboard-box{background:#fff;border-radius:10px;padding:20px;box-shadow:0 0 6px rgba(0,0,0,0.05);height:100%;}
.dashboard-item{background:#F9F9F9;border-radius:8px;padding:10px 12px;margin-bottom:8px;}
.score-circle{position:relative;width:38px;height:38px;border-radius:50%;background:conic-gradient(#578E88 var(--p),#E0E0E0 0);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:600;color:#333;}
.chair-inline{width:12px;height:12px;display:inline-block;vertical-align:middle;margin-right:2px;color:#578E88;}
.tooltip-box{position:relative;display:inline-block;cursor:help;}
.tooltip-text{visibility:hidden;opacity:0;background:#333;color:#fff;text-align:center;border-radius:6px;padding:5px 8px;font-size:11px;position:absolute;z-index:10;bottom:125%;left:50%;transform:translateX(-50%);white-space:nowrap;transition:opacity .2s ease-in-out;}
.tooltip-box:hover .tooltip-text{visibility:visible;opacity:1;}
.activity-dot{display:inline-block;width:8px;height:8px;border-radius:50%;margin-right:2px;}
</style>

<div class="row g-3">

  <!-- TOP KURSE -->
  <div class="col-md-4">
    <div class="dashboard-box">
      <h6 class="fw-semibold mb-3">Top Kurse</h6>
      @foreach($topCourses as $c)
        @php
          $booked = $c->bookings_count;
          $capacity = max($c->number_of_seats,1);
          $filled = round(($booked/$capacity)*10);
          $chairs='';for($i=1;$i<=10;$i++){
            $color=$i<=$filled?'#578E88':'#CCC';
            $chairs.="<span class='chair-inline' style='color:$color;'>$chairSvg</span>";
          }
          $img=$c->thumbnail?RvMedia::getImageUrl($c->thumbnail,'thumb',false,RvMedia::getDefaultImage()):RvMedia::getDefaultImage();
          $url=URL::to('/admin/courses/edit/'.$c->id);
          $date=$c->start_date?Carbon::parse($c->start_date)->format('d.m.y'):'';
        @endphp
        <div class="dashboard-item d-flex justify-content-between align-items-center" onclick="window.location='{{ $url }}'" style="cursor:pointer;">
          <div class="d-flex align-items-center">
            <img src="{{ $img }}" style="width:40px;height:40px;border-radius:6px;object-fit:cover;margin-right:10px;">
            <div>
              <div style="font-size:14px;font-weight:500;">{{ Str::limit($c->name,40) }}</div>
              <div style="font-size:12px;color:#666;">{{ $date }}</div>
            </div>
          </div>
          <div class="text-end" style="font-size:12px;">
            <div class="fw-semibold">{{ number_format($c->price,2,',','.') }} €</div>
            <div>{!! $chairs !!}<span class="text-muted">{{ $booked }}/{{ $capacity }}</span></div>
          </div>
        </div>
      @endforeach
    </div>
  </div>

  <!-- TOP COMMUNITY -->
  <div class="col-md-4">
    <div class="dashboard-box">
      <h6 class="fw-semibold mb-3">Top Community Mitglieder</h6>
      @foreach($topMembers as $m)
        @php
          $avatar=$m->avatar?RvMedia::getImageUrl($m->avatar,'thumb',false,RvMedia::getDefaultImage()):null;
          $score=round(min($m->score,100));
          $recentBookingAt = DB::table('course_bookings')->where('customer_id',$m->id)->max('created_at');
          $days = $recentBookingAt ? now()->diffInDays(Carbon::parse($recentBookingAt)) : 999;
          $actTip = $days <= 10 ? 'Aktiv in den letzten 10 Tagen' : ($days <= 20 ? 'Letzter Login vor 10–20 Tagen' : 'Mehr als 20 Tage inaktiv');
          $scoreTip = 'Score = Buchungen (60%) + Bewertung (30%) + Aktivität (10%)';
        @endphp

        <div class="dashboard-item d-flex justify-content-between align-items-center">
          <div class="d-flex align-items-center">
            <div style="width:40px;height:40px;border-radius:50%;overflow:hidden;margin-right:10px;">
              @if($avatar)
                <img src="{{ $avatar }}" style="width:100%;height:100%;object-fit:cover;">
              @else
                {!! $defaultAvatarSvg !!}
              @endif
            </div>
            <div>
              <div class="fw-semibold" style="font-size:14px;">{{ $m->first_name }} {{ $m->last_name }}</div>
              <div style="font-size:12px;color:#777;">
                <span class="tooltip-box">
                  {!! $m->activity_dots !!}
                  <span class="tooltip-text">{{ $actTip }}</span>
                </span> {{ $m->label }}
              </div>
              <div style="font-size:12px;color:#555;">Buchungen: {{ $m->bookings }}</div>
            </div>
          </div>
          <div class="tooltip-box">
            <div class="score-circle" style="--p: {{ $score }}%;">{{ $score }}</div>
            <span class="tooltip-text">{{ $scoreTip }}</span>
          </div>
        </div>
      @endforeach
    </div>
  </div>

  <!-- LETZTE BUCHUNGEN -->
  <div class="col-md-4">
    <div class="dashboard-box">
      <h6 class="fw-semibold mb-3">Letzte Buchungen</h6>
      @foreach($bookings as $b)
        @php
          $filled=round(($b->booked_count/max($b->number_of_seats,1))*10);
          $chairs='';for($i=1;$i<=10;$i++){
            $color=$i<=$filled?'#578E88':'#CCC';
            $chairs.="<span class='chair-inline' style='color:$color;'>$chairSvg</span>";
          }
          $avatar=$b->avatar?RvMedia::getImageUrl($b->avatar,'thumb',false,RvMedia::getDefaultImage()):null;
          $url=URL::to('/admin/course-bookings/edit/'.$b->booking_id);
          $isPaid=in_array(strtolower($b->status),['paid','completed','success']);
        @endphp
        <div class="dashboard-item" onclick="window.location='{{ $url }}'" style="cursor:pointer;">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <div style="font-size:13px;">{{ Str::limit($b->course_name,40) }}</div>
            <div style="font-size:11px;">{!! $chairs !!}<span class="text-muted">{{ $b->booked_count }}/{{ $b->number_of_seats }}</span></div>
          </div>
          <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
              <div style="width:30px;height:30px;border-radius:50%;overflow:hidden;margin-right:8px;">
                @if($avatar)
                  <img src="{{ $avatar }}" style="width:100%;height:100%;object-fit:cover;">
                @else
                  {!! $defaultAvatarSvg !!}
                @endif
              </div>
              <div style="font-size:12px;">{{ $b->first_name }} {{ $b->last_name }}</div>
            </div>
            <div class="text-end" style="font-size:12px;">
              {{ number_format($b->amount,2,',','.') }} €
              @if($isPaid)
                <span style="background:#A9E6B3;color:#145C2E;font-weight:600;font-size:11px;border-radius:6px;padding:2px 6px;">Bezahlt</span>
              @else
                <span style="background:#FFDAD6;color:#A63C2D;font-weight:600;font-size:11px;border-radius:6px;padding:2px 6px;">Offen</span>
              @endif
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</div>

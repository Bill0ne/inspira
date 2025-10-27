@php
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Carbon\Carbon;

/* === Gewichtungen für Kurs-Ranking === */
$weightBookings = 0.5;
$weightRevenue = 0.3;
$weightOccupancy = 0.2;

/* ==============================
 |  TOP KURSE
 |============================== */
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

/* ==============================
 |  TOP COMMUNITY MEMBERS
 |============================== */
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
        if ($i == 1) {
            if ($recentDays <= 10) $color = '#47B881';
            elseif ($recentDays <= 20) $color = '#F0AD4E';
            else $color = '#E74C3C';
        } else $color = '#CCC';
        $dots .= "<span style='display:inline-block;width:8px;height:8px;border-radius:50%;background:$color;margin-right:2px;'></span>";
    }
    $m->activity_dots = $dots;
}
$topMembers = collect($clients)->sortByDesc('score')->take(5);

/* ==============================
 |  LETZTE BUCHUNGEN
 |============================== */
$bookings = DB::table('course_bookings')
    ->join('courses', 'course_bookings.course_id', '=', 'courses.id')
    ->join('ht_customers', 'course_bookings.customer_id', '=', 'ht_customers.id')
    ->select(
        'course_bookings.id as booking_id',
        'course_bookings.status',
        'course_bookings.amount',
        'course_bookings.created_at',
        'courses.name as course_name',
        'courses.id as course_id',
        'courses.number_of_seats',
        DB::raw('(SELECT COUNT(*) FROM course_bookings cb2 WHERE cb2.course_id = courses.id) as booked_count'),
        'ht_customers.first_name',
        'ht_customers.last_name',
        'ht_customers.avatar'
    )
    ->orderByDesc('course_bookings.created_at')
    ->limit(5)
    ->get();
@endphp

<style>
.dashboard-box {
  background: #fff;
  border-radius: 10px;
  padding: 20px;
  box-shadow: 0 0 6px rgba(0,0,0,0.05);
  height: 100%;
}
.dashboard-item {
  background: #F9F9F9;
  border-radius: 8px;
  padding: 10px 12px;
  margin-bottom: 8px;
}
.chair-icon {
  width: 14px;
  height: 14px;
  filter: brightness(0) saturate(100%) invert(35%) sepia(14%) saturate(993%) hue-rotate(120deg) brightness(96%) contrast(90%);
}
.score-circle {
  position: relative;
  width: 38px;
  height: 38px;
  border-radius: 50%;
  background: conic-gradient(#578E88 var(--p), #E0E0E0 0);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  font-weight: 600;
  color: #333;
}
</style>

<div class="row g-3">

  <!-- ========== TOP KURSE ========== -->
  <div class="col-md-4">
    <div class="dashboard-box">
      <h6 class="fw-semibold mb-3">Top Kurse</h6>
      @foreach($topCourses as $c)
        @php
          $booked = $c->bookings_count;
          $capacity = max($c->number_of_seats,1);
          $progress = round(($booked/$capacity)*100);
          $filled = round($progress/10);
          $chairs = '';
          for ($i=1;$i<=10;$i++){
            $color = $i <= $filled ? '#578E88' : '#CCC';
            $chairs .= "<img src='".asset('storage/general/chair.svg')."' class='chair-icon' style='filter:".($i <= $filled ? "invert(45%) sepia(24%) saturate(372%) hue-rotate(125deg) brightness(95%) contrast(90%);" : "grayscale(1) opacity(0.3);")." margin-right:1px'>";
          }
          $img = $c->thumbnail ? RvMedia::getImageUrl($c->thumbnail,'thumb',false,RvMedia::getDefaultImage()) : RvMedia::getDefaultImage();
          $dateStr = $c->start_date ? Carbon::parse($c->start_date)->format('d.m.y') : '';
          $timeStr = $c->start_date && $c->end_date ? (Carbon::parse($c->start_date)->format('H:i') . ' - ' . Carbon::parse($c->end_date)->format('H:i')) : '';
          $url = URL::to('/admin/courses/edit/'.$c->id);
        @endphp

        <div class="dashboard-item d-flex justify-content-between align-items-center" onclick="window.location='{{ $url }}'" style="cursor:pointer;">
          <div class="d-flex align-items-center">
            <img src="{{ $img }}" style="width:40px;height:40px;border-radius:6px;object-fit:cover;margin-right:10px;">
            <div>
              <div style="font-size:14px;font-weight:500;">{{ Str::limit($c->name,40) }}</div>
              <div style="font-size:12px;color:#666;">{{ $dateStr }} {{ $timeStr }}</div>
            </div>
          </div>
          <div class="text-end" style="font-size:12px;">
            <div class="fw-semibold">{{ number_format($c->price,2,',','.') }} €</div>
            <div>{!! $chairs !!} <span class="text-muted">{{ $booked }}/{{ $capacity }}</span></div>
          </div>
        </div>
      @endforeach
    </div>
  </div>

  <!-- ========== TOP COMMUNITY ========== -->
  <div class="col-md-4">
    <div class="dashboard-box">
      <h6 class="fw-semibold mb-3">Top Community Mitglieder</h6>
      @foreach($topMembers as $m)
        @php
          $avatar = $m->avatar ? RvMedia::getImageUrl($m->avatar,'thumb',false,RvMedia::getDefaultImage()) : RvMedia::getDefaultImage();
          $score = round(min($m->score,100));
        @endphp
        <div class="dashboard-item d-flex justify-content-between align-items-center">
          <div class="d-flex align-items-center">
            <img src="{{ $avatar }}" style="width:40px;height:40px;border-radius:50%;object-fit:cover;margin-right:10px;">
            <div>
              <div class="fw-semibold" style="font-size:14px;">{{ $m->first_name }} {{ $m->last_name }}</div>
              <div style="font-size:12px;color:#777;">{!! $m->activity_dots !!} {{ $m->label }}</div>
              <div style="font-size:12px;color:#555;">Buchungen: {{ $m->bookings }}</div>
            </div>
          </div>
          <div class="score-circle" style="--p: {{ $score }}%;">
            {{ $score }}
          </div>
        </div>
      @endforeach
    </div>
  </div>

  <!-- ========== LETZTE BUCHUNGEN ========== -->
  <div class="col-md-4">
    <div class="dashboard-box">
      <h6 class="fw-semibold mb-3">Letzte Buchungen</h6>
      @foreach($bookings as $b)
        @php
          $booked = $b->booked_count;
          $capacity = max($b->number_of_seats,1);
          $progress = round(($booked/$capacity)*100);
          $filled = round($progress/10);
          $chairs = '';
          for ($i=1;$i<=10;$i++){
            $color = $i <= $filled ? '#578E88' : '#CCC';
            $chairs .= "<img src='".asset('/public/themes/riorelax/images/icons/chair.svg')."' class='chair-icon' style='filter:".($i <= $filled ? "invert(45%) sepia(24%) saturate(372%) hue-rotate(125deg) brightness(95%) contrast(90%);" : "grayscale(1) opacity(0.3);")." margin-right:1px'>";
          }
          $avatar = $b->avatar ? RvMedia::getImageUrl($b->avatar,'thumb',false,RvMedia::getDefaultImage()) : RvMedia::getDefaultImage();
          $url = URL::to('/admin/course-bookings/edit/'.$b->booking_id);
          $isPaid = in_array(strtolower($b->status), ['paid','completed','success']);
        @endphp

        <div class="dashboard-item" onclick="window.location='{{ $url }}'" style="cursor:pointer;">
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
              {{ number_format($b->a_

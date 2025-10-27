@php
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

/* === Parameter-Gewichtungen === */
$weightBookings = 0.5;
$weightRevenue = 0.3;
$weightOccupancy = 0.2;

/* === Top-Kurse ermitteln === */
$courses = DB::table('courses')
    ->leftJoin('course_bookings', 'courses.id', '=', 'course_bookings.course_id')
    ->select(
        'courses.id',
        'courses.name',
        'courses.image',
        'courses.total_slots',
        'courses.booked_slots',
        DB::raw('COUNT(course_bookings.id) as bookings'),
        DB::raw('SUM(course_bookings.price) as revenue'),
        DB::raw('MAX(course_bookings.session_date) as session_date'),
        DB::raw('MAX(course_bookings.session_start_time) as start_time'),
        DB::raw('MAX(course_bookings.session_end_time) as end_time')
    )
    ->groupBy('courses.id', 'courses.name', 'courses.image', 'courses.total_slots', 'courses.booked_slots')
    ->get();

foreach ($courses as $c) {
    $bookings = $c->bookings ?? 0;
    $revenue = $c->revenue ?? 0;
    $occupancy = $bookings / max($c->total_slots, 1);
    $c->score =
        ($bookings * $weightBookings) +
        ($revenue * $weightRevenue / 100) +
        ($occupancy * 100 * $weightOccupancy);
}

$topCourses = collect($courses)->sortByDesc('score')->take(5);

/* === Top Community Mitglieder === */
$members = DB::table('customers')
    ->select(
        'customers.id',
        'customers.first_name',
        'customers.last_name',
        'customers.avatar',
        'customers.type', // coach | client
        'customers.last_login_at'
    )
    ->get();

foreach ($members as $m) {
    $coursesCount = DB::table('courses')->where('coach_id', $m->id)->count();
    $bookingsCount = DB::table('course_bookings')->where('customer_id', $m->id)->count();
    $feedbacks = DB::table('reviews')->where('customer_id', $m->id)->count();
    $activityDays = rand(0, 20); // Beispielwert – später dynamisch via Logins
    $rating = 4.5; // Platzhalter

    if ($m->type === 'coach') {
        $m->score = ($coursesCount * 0.4) + ($bookingsCount * 0.4) + ($rating * 0.2);
        $m->label = 'Top Coach';
    } else {
        $m->score = ($bookingsCount * 0.6) + ($activityDays * 0.3) + ($feedbacks * 0.1);
        $m->label = 'Top Client';
    }

    // Aktivitätsstatusfarben (max 5 Punkte)
    $recentActivity = now()->diffInDays($m->last_login_at ?? now()->subDays(40));
    $dots = '';
    for ($i = 1; $i <= 5; $i++) {
        if ($recentActivity <= 10 && $i == 1) {
            $color = '#47B881'; // grün
        } elseif ($recentActivity <= 20 && $i == 1) {
            $color = '#F0AD4E'; // orange
        } elseif ($recentActivity > 20 && $i == 1) {
            $color = '#E74C3C'; // rot
        } else {
            $color = '#CCC'; // grau (leer)
        }
        $dots .= "<span style='display:inline-block;width:8px;height:8px;border-radius:50%;background:$color;margin-right:2px;'></span>";
    }

    $m->activity_dots = $dots;
}

$topMembers = collect($members)->sortByDesc('score')->take(5);

/* === Letzte Buchungen === */
$bookings = DB::table('course_bookings')
    ->join('courses', 'course_bookings.course_id', '=', 'courses.id')
    ->join('customers', 'course_bookings.customer_id', '=', 'customers.id')
    ->select(
        'course_bookings.id as booking_id',
        'course_bookings.payment_status',
        'course_bookings.created_at',
        'course_bookings.session_date',
        'course_bookings.session_start_time',
        'course_bookings.session_end_time',
        'courses.name as course_name',
        'courses.total_slots',
        'courses.booked_slots',
        'customers.first_name',
        'customers.last_name',
        'customers.avatar',
        'course_bookings.price'
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
      <!-- ========== Top Kurse ========== -->
      <div class="col-md-4">
        <div class="p-3 bg-white rounded shadow-sm h-100">
          <h6 class="fw-semibold mb-3">Top Kurse</h6>
          @foreach($topCourses as $c)
            @php
              $progress = $c->total_slots > 0 ? round(($c->booked_slots / $c->total_slots) * 100) : 0;
              $chairs = '';
              $filled = round($progress / 10);
              for ($i=1;$i<=10;$i++){
                $chairs .= $i <= $filled ? '🪑' : '<span style="opacity:0.2;">🪑</span>';
              }
              $img = $c->image ? RvMedia::getImageUrl($c->image,'thumb',false,RvMedia::getDefaultImage()) : RvMedia::getDefaultImage();
            @endphp
            <div class="d-flex align-items-center justify-content-between mb-2 p-2 rounded hover:bg-light" style="cursor:pointer;">
              <div class="d-flex align-items-center">
                <img src="{{ $img }}" style="width:40px;height:40px;border-radius:6px;object-fit:cover;margin-right:10px;">
                <div>
                  <div style="font-size:14px;font-weight:500;">{{ Str::limit($c->name,40) }}</div>
                  <div style="font-size:12px;color:#666;">
                    {{ date('d.m',strtotime($c->session_date ?? now())) }}
                    {{ substr($c->start_time ?? '00:00',0,5) }}–{{ substr($c->end_time ?? '00:00',0,5) }}
                  </div>
                </div>
              </div>
              <div class="text-end" style="font-size:12px;">
                {!! $chairs !!} <span class="text-muted">{{ $c->booked_slots }}/{{ $c->total_slots }}</span>
              </div>
            </div>
          @endforeach
        </div>
      </div>

      <!-- ========== Top Community Mitglieder ========== -->
      <div class="col-md-4">
        <div class="p-3 bg-white rounded shadow-sm h-100">
          <h6 class="fw-semibold mb-3">Top Community Mitglieder</h6>
          @foreach($topMembers as $m)
            @php
              $avatar = $m->avatar ? RvMedia::getImageUrl($m->avatar,'thumb',false,RvMedia::getDefaultImage()) : RvMedia::getDefaultImage();
            @endphp
            <div class="d-flex justify-content-between align-items-center mb-3 p-2 rounded hover:bg-light">
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

      <!-- ========== Letzte Buchungen ========== -->
      <div class="col-md-4">
        <div class="p-3 bg-white rounded shadow-sm h-100">
          <h6 class="fw-semibold mb-3">Letzte Buchungen</h6>
          @foreach($bookings as $b)
            @php
              $progress = $b->total_slots>0 ? round(($b->booked_slots/$b->total_slots)*100) : 0;
              $filledChairs = round($progress/10);
              $chairs = '';
              for ($i=1;$i<=10;$i++){
                $chairs .= $i <= $filledChairs ? '🪑' : '<span style="opacity:0.2;">🪑</span>';
              }
              $avatar = $b->avatar ? RvMedia::getImageUrl($b->avatar,'thumb',false,RvMedia::getDefaultImage()) : RvMedia::getDefaultImage();
              $url = URL::to('/admin/course-bookings/edit/'.$b->booking_id);
            @endphp
            <div onclick="window.location='{{ $url }}'"
                 style="cursor:pointer;background:#fff;border-radius:8px;padding:8px 10px;margin-bottom:8px;border:1px solid #eee;">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <div style="font-size:13px;">{{ Str::limit($b->course_name,40) }}</div>
                <div style="font-size:11px;">{!! $chairs !!} <span class="text-muted">{{ $b->booked_slots }}/{{ $b->total_slots }}</span></div>
              </div>
              <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                  <img src="{{ $avatar }}" style="width:30px;height:30px;border-radius:50%;object-fit:cover;margin-right:8px;">
                  <div style="font-size:12px;">{{ $b->first_name }} {{ $b->last_name }}</div>
                </div>
                <div class="text-end" style="font-size:12px;">
                  {{ number_format($b->price,2,',','.') }} €
                  @if($b->payment_status==='paid')
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
  </div>
</div>

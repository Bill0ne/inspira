@php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

$user = Auth::user();
$today = Carbon::today();
$yesterday = Carbon::yesterday();
$monthStart = Carbon::now()->startOfMonth();
$prevMonthStart = Carbon::now()->subMonth()->startOfMonth();
$prevMonthEnd = Carbon::now()->subMonth()->endOfMonth();
$monthName = Carbon::now()->translatedFormat('M');

/* === Inspirationen des Tages === */
$quotes = [
  "Heute musst du nicht alles schaffen. Nur das, was dich wirklich weiterbringt.",
  "Räume entstehen im Außen – Balance im Inneren.",
  "Atme tief. Das Leben läuft nicht davon.",
  "Erfolg beginnt dort, wo Ruhe Platz findet.",
  "Der schönste Plan ist wertlos, wenn du dich selbst vergisst.",
  "Liebe wächst, wenn du dir Zeit nimmst, sie zu fühlen.",
  "Heute ist der beste Tag, um neu zu beginnen – nicht perfekt, aber echt.",
  "Je ruhiger du wirst, desto klarer siehst du.",
  "Lass los, was dich müde macht. Mach Platz für das, was dich nährt.",
  "Alles beginnt mit einem Atemzug.",
];
$quote = $quotes[date('z') % count($quotes)];

/* === Vergleichsfunktion === */
function diffChip($current, $prev){
    if($prev == 0 && $current == 0) return "<span class='chip neutral'>0%</span>";
    if($prev == 0) return "<span class='chip up'>+100%</span>";
    $diff = round((($current - $prev) / max($prev,1)) * 100,1);
    $class = $diff > 0 ? 'up' : ($diff < 0 ? 'down' : 'neutral');
    $sign = $diff > 0 ? '+' : '';
    return "<span class='chip $class'>{$sign}{$diff}%</span>";
}

/* === Statistiken === */
// Buchungen
$bookingsToday = DB::table('course_bookings')->whereDate('created_at',$today)->count();
$bookingsYesterday = DB::table('course_bookings')->whereDate('created_at',$yesterday)->count();
$bookingsMonth = DB::table('course_bookings')->whereBetween('created_at',[$monthStart,Carbon::now()])->count();
$bookingsPrevMonth = DB::table('course_bookings')->whereBetween('created_at',[$prevMonthStart,$prevMonthEnd])->count();
$bookingsChart = DB::table('course_bookings')
    ->select(DB::raw('DATE(created_at) as d'), DB::raw('COUNT(*) as c'))
    ->whereBetween('created_at',[Carbon::now()->subDays(6), Carbon::now()])
    ->groupBy('d')->orderBy('d')->pluck('c')->toArray();

// Umsatz
$revenueToday = DB::table('payments')->whereDate('created_at',$today)->whereIn('status',['paid','completed','success'])->sum('amount');
$revenueYesterday = DB::table('payments')->whereDate('created_at',$yesterday)->whereIn('status',['paid','completed','success'])->sum('amount');
$revenueMonth = DB::table('payments')->whereBetween('created_at',[$monthStart,Carbon::now()])->whereIn('status',['paid','completed','success'])->sum('amount');
$revenuePrevMonth = DB::table('payments')->whereBetween('created_at',[$prevMonthStart,$prevMonthEnd])->whereIn('status',['paid','completed','success'])->sum('amount');
$revenueChart = DB::table('payments')
    ->select(DB::raw('DATE(created_at) as d'), DB::raw('SUM(amount) as s'))
    ->whereBetween('created_at',[Carbon::now()->subDays(6), Carbon::now()])
    ->whereIn('status',['paid','completed','success'])
    ->groupBy('d')->orderBy('d')->pluck('s')->toArray();

// Kurse
$coursesToday = DB::table('courses')->whereDate('created_at',$today)->count();
$coursesYesterday = DB::table('courses')->whereDate('created_at',$yesterday)->count();
$coursesMonth = DB::table('courses')->whereBetween('created_at',[$monthStart,Carbon::now()])->count();
$coursesPrevMonth = DB::table('courses')->whereBetween('created_at',[$prevMonthStart,$prevMonthEnd])->count();
$coursesChart = DB::table('courses')
    ->select(DB::raw('DATE(created_at) as d'), DB::raw('COUNT(*) as c'))
    ->whereBetween('created_at',[Carbon::now()->subDays(6), Carbon::now()])
    ->groupBy('d')->orderBy('d')->pluck('c')->toArray();

// Kunden
$customersToday = DB::table('ht_customers')->whereDate('created_at',$today)->count();
$customersYesterday = DB::table('ht_customers')->whereDate('created_at',$yesterday)->count();
$customersMonth = DB::table('ht_customers')->whereBetween('created_at',[$monthStart,Carbon::now()])->count();
$customersPrevMonth = DB::table('ht_customers')->whereBetween('created_at',[$prevMonthStart,$prevMonthEnd])->count();
$customersChart = DB::table('ht_customers')
    ->select(DB::raw('DATE(created_at) as d'), DB::raw('COUNT(*) as c'))
    ->whereBetween('created_at',[Carbon::now()->subDays(6), Carbon::now()])
    ->groupBy('d')->orderBy('d')->pluck('c')->toArray();

/* === Cards definieren === */
$stats = [
    ['label'=>'Buchungen','icon'=>'fal fa-calendar-check','today'=>$bookingsToday,'month'=>$bookingsMonth,
     'diff_today'=>diffChip($bookingsToday,$bookingsYesterday),'diff_month'=>diffChip($bookingsMonth,$bookingsPrevMonth),'chart'=>$bookingsChart],
    ['label'=>'Umsatz','icon'=>'fal fa-coins','today'=>number_format($revenueToday,2,',','.'),'month'=>number_format($revenueMonth,2,',','.'),
     'diff_today'=>diffChip($revenueToday,$revenueYesterday),'diff_month'=>diffChip($revenueMonth,$revenuePrevMonth),'chart'=>$revenueChart],
    ['label'=>'Kurse','icon'=>'fal fa-chalkboard-teacher','today'=>$coursesToday,'month'=>$coursesMonth,
     'diff_today'=>diffChip($coursesToday,$coursesYesterday),'diff_month'=>diffChip($coursesMonth,$coursesPrevMonth),'chart'=>$coursesChart],
    ['label'=>'Kunden','icon'=>'fal fa-user-friends','today'=>$customersToday,'month'=>$customersMonth,
     'diff_today'=>diffChip($customersToday,$customersYesterday),'diff_month'=>diffChip($customersMonth,$customersPrevMonth),'chart'=>$customersChart],
];
@endphp

<style>
.greeting-box {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  background: #fff;
  border-radius: 10px;
  padding: 25px 30px;
  box-shadow: 0 0 6px rgba(0,0,0,0.05);
}
.greeting-left { flex: 1; }
.greeting-left h4 { font-weight: 600; font-size: 20px; color: #111; margin-bottom: 10px; }
.greeting-left .quote-label { font-size: 12px; color: #888; }
.greeting-left .quote-text { color: #578E88; font-size: 14px; line-height: 1.4; margin-top: 3px; max-width: 400px; }
.greeting-right {
  flex: 0 0 auto;
  display: grid;
  grid-template-columns: repeat(2, 240px);
  gap: 14px;
  justify-content: flex-end;
}
.stat-card {
  background: #F3F3F3;
  border-radius: 8px;
  padding: 10px 14px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  transition: all .2s ease;
}
.stat-card:hover { background: #ECECEC; }
.stat-header { display: flex; align-items: center; margin-bottom: 6px; }
.stat-icon {
  background: #578E88;
  color: #fff;
  width: 40px;
  height: 40px;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  margin-right: 10px;
}
.stat-title { font-size: 13px; font-weight: 600; color: #333; }
.stat-body { display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px; }
.stat-body p { margin: 0; font-size: 11px; color: #555; }
.stat-body strong { font-size: 13px; color: #222; }
.chip { font-size: 10px; border-radius: 10px; padding: 1px 6px; margin-left: 4px; white-space: nowrap; }
.chip.up { background: #D9F2E3; color: #137B40; font-weight: 600; }
.chip.down { background: #FFDAD6; color: #A63C2D; font-weight: 600; }
.chip.neutral { background: #E0E0E0; color: #555; }
.chart-line { width: 100%; height: 24px; margin-top: 6px; }
.chart-line svg { width: 100%; height: 100%; stroke: #578E88; stroke-width: 2; fill: rgba(87,142,136,0.15); }
</style>

<div class="greeting-box">
  <!-- === LINKS === -->
  <div class="greeting-left">
    <h4>Willkommen zurück bei Inspira {{ $user->first_name ?? $user->name }}.</h4>
    <div class="quote-label">🌿 Deine <strong>INSPIRation des Tages</strong>:</div>
    <div class="quote-text">{{ $quote }}</div>
  </div>

  <!-- === RECHTS === -->
  <div class="greeting-right">
    @foreach($stats as $s)
      <div class="stat-card">
        <div>
          <div class="stat-header">
            <div class="stat-icon"><i class="{{ $s['icon'] }}"></i></div>
            <div class="stat-title">{{ $s['label'] }}</div>
          </div>
          <div class="stat-body">
            <p>Heute {!! $s['diff_today'] !!}</p>
            <strong>{{ $s['today'] }}</strong>
          </div>
          <div class="stat-body">
            <p>Gesamt {{ $monthName }} {!! $s['diff_month'] !!}</p>
            <strong>{{ $s['month'] }}</strong>
          </div>
        </div>
        <div class="chart-line">
          @php
            $points = $s['chart'] ?: [0];
            $max = max($points);
            $min = min($points);
            $range = max($max - $min, 1);
            $coords = [];
            foreach($points as $i => $v){
              $x = ($i / max(count($points)-1,1)) * 100;
              $y = 100 - (($v - $min) / $range) * 100;
              $coords[] = "$x,$y";
            }
          @endphp
          <svg viewBox="0 0 100 100" preserveAspectRatio="none">
            <polyline points="{{ implode(' ', $coords) }}" vector-effect="non-scaling-stroke" />
            <polygon points="{{ implode(' ', $coords) }} 100,100 0,100" />
          </svg>
        </div>
      </div>
    @endforeach
  </div>
</div>

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
    "Jeder kleine Schritt ist ein Fortschritt.",
    "Manchmal ist „Nichts tun“ der wichtigste Termin im Kalender.",
    "Räume verändern Menschen – und Menschen gestalten Räume.",
    "Balance ist kein Zustand. Es ist eine tägliche Entscheidung.",
    "Wo Liebe wohnt, entsteht Energie.",
    "Ein voller Kalender ist kein Zeichen von Erfüllung.",
    "Wer in sich ankommt, kommt überall hin.",
    "Dein Körper hört, was dein Kopf sagt – sprich freundlich mit dir.",
    "Zwischen Reiz und Reaktion liegt Raum. Nutze ihn.",
    "Glück entsteht, wenn du aufhörst, es zu suchen.",
    "Heute darf leicht sein.",
    "Stille ist kein Mangel an Geräuschen – sondern ein Überfluss an Klarheit.",
    "Sorge gut für dich. Du bist der wichtigste Mensch in deinem Leben.",
    "Das Leben spricht – aber nur, wenn du zuhörst.",
    "Energie folgt der Aufmerksamkeit. Wähle weise, wohin du schaust.",
    "Entspannung ist kein Luxus – sie ist Voraussetzung für Wachstum.",
    "Du musst nicht perfekt sein, um Frieden zu finden.",
    "Schönheit liegt nicht im Raum, sondern in der Art, wie du ihn fühlst.",
    "Heute ist genug. Du bist genug.",
    "Was du suchst, sucht dich auch.",
    "Lass dich nicht hetzen – Qualität wächst nicht in Eile.",
    "Stärke zeigt sich nicht im Tempo, sondern im Vertrauen.",
    "Zeit ist nicht das Problem – Priorität ist die Lösung.",
    "Du kannst nicht immer alles kontrollieren, aber immer deinen Atem.",
    "Zwischen Chaos und Klarheit liegt ein tiefer Atemzug.",
    "Es ist mutig, Pause zu machen.",
    "Wer sich selbst versteht, versteht die Welt.",
    "Gib deinem Tag Richtung – nicht Druck.",
    "Energie kommt nicht vom Tun, sondern vom Sinn.",
    "Heute darf einfach sein, was ist.",
    "Entfalte dich, statt dich zu beweisen.",
    "Dein Wert hängt nicht an deiner To-do-Liste.",
    "Vertraue dem Tempo, das dein Herz vorgibt.",
    "Du musst nicht immer stärker werden – manchmal reicht es, weicher zu werden.",
    "Dankbarkeit verwandelt jeden Raum in Zuhause.",
    "Jeder Tag ist ein neuer Versuch, bei dir anzukommen.",
    "Weniger Lärm. Mehr Leben.",
    "Wer langsamer wird, hört wieder das Wesentliche.",
    "Achte auf deine Energie – sie ist deine Sprache an die Welt.",
    "Heute ist kein Tag zum Rennen, sondern zum Sein.",
    "Alles beginnt mit einem Atemzug.",
];
$quote = $quotes[date('z') % count($quotes)];

function diffChipAbs($current, $prev, $currency = false) {
    $diff = $current - $prev;
    if ($diff == 0) return "<span class='chip neutral'>0</span>";
    $class = $diff > 0 ? 'up' : 'down';
    $sign = $diff > 0 ? '+' : '–';
    $value = $currency
        ? $sign . number_format(abs($diff), 2, ',', '.') . ' €'
        : $sign . number_format(abs($diff), 0, ',', '.');
    return "<span class='chip {$class}'>{$value}</span>";
}

/* === STATISTIKEN === */
$paid = ['paid','completed','success'];

/* Buchungen */
$bookingsToday = DB::table('course_bookings')->whereDate('created_at',$today)->count();
$bookingsYesterday = DB::table('course_bookings')->whereDate('created_at',$yesterday)->count();
$bookingsMonth = DB::table('course_bookings')->whereBetween('created_at',[$monthStart,now()])->count();
$bookingsPrevMonth = DB::table('course_bookings')->whereBetween('created_at',[$prevMonthStart,$prevMonthEnd])->count();
$bookingsChart = DB::table('course_bookings')
    ->selectRaw('DATE(created_at) as d, COUNT(*) as c')
    ->whereBetween('created_at',[now()->subDays(6), now()])
    ->groupBy('d')->orderBy('d')->pluck('c')->toArray();

/* Umsatz */
$revenueToday = DB::table('payments')->whereDate('created_at',$today)->whereIn('status',$paid)->sum('amount');
$revenueYesterday = DB::table('payments')->whereDate('created_at',$yesterday)->whereIn('status',$paid)->sum('amount');
$revenueMonth = DB::table('payments')->whereBetween('created_at',[$monthStart,now()])->whereIn('status',$paid)->sum('amount');
$revenuePrevMonth = DB::table('payments')->whereBetween('created_at',[$prevMonthStart,$prevMonthEnd])->whereIn('status',$paid)->sum('amount');
$revenueChart = DB::table('payments')
    ->selectRaw('DATE(created_at) as d, SUM(amount) as s')
    ->whereBetween('created_at',[now()->subDays(6), now()])
    ->whereIn('status',$paid)->groupBy('d')->orderBy('d')->pluck('s')->toArray();

/* Kurse */
$coursesToday = DB::table('courses')->whereDate('created_at',$today)->count();
$coursesYesterday = DB::table('courses')->whereDate('created_at',$yesterday)->count();
$coursesMonth = DB::table('courses')->whereBetween('created_at',[$monthStart,now()])->count();
$coursesPrevMonth = DB::table('courses')->whereBetween('created_at',[$prevMonthStart,$prevMonthEnd])->count();
$coursesChart = DB::table('courses')
    ->selectRaw('DATE(created_at) as d, COUNT(*) as c')
    ->whereBetween('created_at',[now()->subDays(6), now()])
    ->groupBy('d')->orderBy('d')->pluck('c')->toArray();

/* Kunden */
$customersToday = DB::table('ht_customers')->whereDate('created_at',$today)->count();
$customersYesterday = DB::table('ht_customers')->whereDate('created_at',$yesterday)->count();
$customersMonth = DB::table('ht_customers')->whereBetween('created_at',[$monthStart,now()])->count();
$customersPrevMonth = DB::table('ht_customers')->whereBetween('created_at',[$prevMonthStart,$prevMonthEnd])->count();
$customersChart = DB::table('ht_customers')
    ->selectRaw('DATE(created_at) as d, COUNT(*) as c')
    ->whereBetween('created_at',[now()->subDays(6), now()])
    ->groupBy('d')->orderBy('d')->pluck('c')->toArray();

/* Kartenmodell */
$stats = [
    [
        'label'=>'Buchungen','icon'=>'fal fa-calendar-check',
        'today'=>$bookingsToday,'month'=>$bookingsMonth,
        'diff_today'=>diffChipAbs($bookingsToday,$bookingsYesterday),
        'diff_month'=>diffChipAbs($bookingsMonth,$bookingsPrevMonth),
        'chart'=>$bookingsChart
    ],
    [
        'label'=>'Umsatz','icon'=>'fal fa-coins',
        'today'=>number_format($revenueToday,2,',','.').' €',
        'month'=>number_format($revenueMonth,2,',','.').' €',
        'diff_today'=>diffChipAbs($revenueToday,$revenueYesterday,true),
        'diff_month'=>diffChipAbs($revenueMonth,$revenuePrevMonth,true),
        'chart'=>$revenueChart
    ],
    [
        'label'=>'Kurse','icon'=>'fal fa-chalkboard-teacher',
        'today'=>$coursesToday,'month'=>$coursesMonth,
        'diff_today'=>diffChipAbs($coursesToday,$coursesYesterday),
        'diff_month'=>diffChipAbs($coursesMonth,$coursesPrevMonth),
        'chart'=>$coursesChart
    ],
    [
        'label'=>'Kunden','icon'=>'fal fa-user-friends',
        'today'=>$customersToday,'month'=>$customersMonth,
        'diff_today'=>diffChipAbs($customersToday,$customersYesterday),
        'diff_month'=>diffChipAbs($customersMonth,$customersPrevMonth),
        'chart'=>$customersChart
    ],
];
@endphp

<style>
.greeting-box {
  display: flex; justify-content: space-between; align-items: flex-start;
  background: #fff; border-radius: 10px; padding: 25px 30px;
  box-shadow: 0 0 6px rgba(0,0,0,0.05);
}
.greeting-left { flex: 1; }
.greeting-left h4 { font-weight: 600; font-size: 20px; margin-bottom: 8px; }
.greeting-left .quote-label { font-size: 12px; color: #777; }
.greeting-left .quote-text { font-size: 14px; color: #578E88; margin-top: 3px; }

.greeting-right {
  display: grid; grid-template-columns: repeat(2, 320px);
  gap: 16px; flex: 0 0 auto;
}

.stat-card {
  background: #F3F3F3;
  border-radius: 10px;
  padding: 10px 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  transition: all .2s ease;
}
.stat-card:hover { background: #ECECEC; }

.stat-left { display: flex; align-items: center; gap: 14px; flex: 1; }
.stat-icon {
  background: #578E88;
  color: #fff;
  width: 48px; height: 48px;
  border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  font-size: 22px;
}
.stat-info { display: flex; flex-direction: column; gap: 6px; font-size: 13px; color: #333; }
.stat-row { display: flex; align-items: center; justify-content: space-between; gap: 8px; }
.stat-row p { margin: 0; color: #666; font-size: 12px; }
.stat-row strong { font-size: 14px; font-weight: 600; color: #111; }
.chip { font-size: 11px; border-radius: 8px; padding: 2px 8px; }
.chip.up { background: #D9F2E3; color: #137B40; }
.chip.down { background: #FFDAD6; color: #A63C2D; }
.chip.neutral { background: #E0E0E0; color: #555; }
.chart-line { width: 70px; height: 28px; margin-left: 8px; flex-shrink: 0; }
.chart-line svg { width: 100%; height: 100%; stroke: #578E88; stroke-width: 2; fill: rgba(87,142,136,0.15); }
</style>

<div class="greeting-box">
  <div class="greeting-left">
    <h4>Willkommen zurück bei Inspira {{ $user->first_name ?? $user->name }}.</h4>
    <div class="quote-label">🌿 Deine <strong>INSPIRation des Tages</strong>:</div>
    <div class="quote-text">{{ $quote }}</div>
  </div>

  <div class="greeting-right">
    @foreach($stats as $s)
      <div class="stat-card">
        <div class="stat-left">
          <div class="stat-icon"><i class="{{ $s['icon'] }}"></i></div>
          <div class="stat-info">
            <div class="stat-row">
              <p>Heute:</p>
              <strong>{{ $s['today'] }}</strong>
              {!! $s['diff_today'] !!}
            </div>
            <div class="stat-row">
              <p>Gesamt {{ $monthName }}:</p>
              <strong>{{ $s['month'] }}</strong>
              {!! $s['diff_month'] !!}
            </div>
          </div>
        </div>
        <div class="chart-line">
          @php
            $points = $s['chart'] ?: [0];
            $max = max($points); $min = min($points);
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

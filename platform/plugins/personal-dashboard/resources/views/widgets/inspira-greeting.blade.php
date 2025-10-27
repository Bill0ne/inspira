@php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/* === User & Zeitkontext === */
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

// 1️⃣ Buchungen
$bookingsToday = DB::table('course_bookings')->whereDate('created_at',$today)->count();
$bookingsYesterday = DB::table('course_bookings')->whereDate('created_at',$yesterday)->count();
$bookingsMonth = DB::table('course_bookings')->whereBetween('created_at',[$monthStart,Carbon::now()])->count();
$bookingsPrevMonth = DB::table('course_bookings')->whereBetween('created_at',[$prevMonthStart,$prevMonthEnd])->count();
$bookingsChart = DB::table('course_bookings')
    ->select(DB::raw('DATE(created_at) as d'), DB::raw('COUNT(*) as c'))
    ->whereBetween('created_at',[Carbon::now()->subDays(6), Carbon::now()])
    ->groupBy('d')->orderBy('d')->pluck('c')->toArray();

// 2️⃣ Umsatz
$revenueToday = DB::table('payments')->whereDate('created_at',$today)->whereIn('status',['paid','completed','success'])->sum('amount');
$revenueYesterday = DB::table('payments')->whereDate('created_at',$yesterday)->whereIn('status',['paid','completed','success'])->sum('amount');
$revenueMonth = DB::table('payments')->whereBetween('created_at',[$monthStart,Carbon::now()])->whereIn('status',['paid','completed','success'])->sum('amount');
$revenuePrevMonth = DB::table('payments')->whereBetween('created_at',[$prevMonthStart,$prevMonthEnd])->whereIn('status',['paid','completed','success'])->sum('amount');
$revenueChart = DB::table('payments')
    ->select(DB::raw('DATE(created_at) as d'), DB::raw('SUM(amount) as s'))
    ->whereBetween('created_at',[Carbon::now()->subDays(6), Carbon::now()])
    ->whereIn('status',['paid','completed','success'])
    ->groupBy('d')->orderBy('d')->pluck('s')->toArray();

// 3️⃣ Kurse
$coursesToday = DB::table('courses')->whereDate('created_at',$today)->count();
$coursesYesterday = DB::table('courses')->whereDate('created_at',$yesterday)->count();
$coursesMonth = DB::table('courses')->whereBetween('created_at',[$monthStart,Carbon::now()])->count();
$coursesPrevMonth = DB::table('courses')->whereBetween('created_at',[$prevMonthStart,$prevMonthEnd])->count();
$coursesChart = DB::table('courses')
    ->select(DB::raw('DATE(created_at) as d'), DB::raw('COUNT(*) as c'))
    ->whereBetween('created_at',[Carbon::now()->subDays(6), Carbon::now()])
    ->groupBy('d')->orderBy('d')->pluck('c')->toArray();

// 4️⃣ Kunden
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
    [
        'label'=>'Buchungen','icon'=>'fal fa-calendar-check',
        'today'=>$bookingsToday,'month'=>$bookingsMonth,
        'diff_today'=>diffChip($bookingsToday,$bookingsYesterday),
        'diff_month'=>diffChip($bookingsMonth,$bookingsPrevMonth),
        'chart'=>$bookingsChart
    ],
    [
        'label'=>'Umsatz','icon'=>'fal fa-coins',
        'today'=>number_format($revenueToday,2,',','.'),'month'=>number_format($revenueMonth,2,',','.'),
        'diff_today'=>diffChip($revenueToday,$revenueYesterday),
        'diff_month'=>diffChip($revenueMonth,$revenuePrevMonth),
        'chart'=>$revenueChart
    ],
    [
        'label'=>'Kurse','icon'=>'fal fa-chalkboard-teacher',
        'today'=>$coursesToday,'month'=>$coursesMonth,
        'diff_today'=>diffChip($coursesToday,$coursesYesterday),
        'diff_month'=>diffChip($coursesMonth,$coursesPrevMonth),
        'chart'=>$coursesChart
    ],
    [
        'label'=>'Kunden','icon'=>'fal fa-user-friends',
        'today'=>$customersToday,'month'=>$customersMonth,
        'diff_today'=>diffChip($customersToday,$customersYesterday),
        'diff_month'=>diffChip($customersMonth,$customersPrevMonth),
        'chart'=>$customersChart
    ],
];
@endphp

<style>
.stat-grid{display:grid;grid-template-columns:repeat(2,280px);gap:16px;}
.stat-card{background:#F3F3F3;border-radius:8px;padding:12px 16px 6px 16px;display:flex;flex-direction:column;justify-content:space-between;}
.stat-header{display:flex;align-items:center;margin-bottom:6px;}
.stat-icon{background:#578E88;color:#fff;width:42px;height:42px;border-radius:6px;
display:flex;align-items:center;justify-content:center;font-size:20px;margin-right:10px;}
.stat-title{font-size:14px;font-weight:600;color:#333;}
.stat-body{display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;}
.stat-body p{margin:0;font-size:11px;color:#555;}
.stat-body strong{font-size:13px;}
.chip{font-size:10px;border-radius:10px;padding:1px 6px;margin-left:4px;}
.chip.up{background:#D9F2E3;color:#137B40;font-weight:600;}
.chip.down{background:#FFDAD6;color:#A63C2D;font-weight:600;}
.chip.neutral{background:#E0E0E0;color:#555;}
.chart-line{width:100%;height:26px;margin-top:4px;position:relative;}
.chart-line svg{width:100%;height:100%;stroke:#578E88;stroke-width:2;fill:rgba(87,142,136,0.15);}
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

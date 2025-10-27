@php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/* === User & Zeitkontext === */
$user = Auth::user();
$today = Carbon::today();
$monthStart = Carbon::now()->startOfMonth();
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

/* === Statistiken === */

// 1️⃣ Buchungen
$bookingsToday = DB::table('course_bookings')
    ->whereDate('created_at', $today)
    ->count();
$bookingsMonth = DB::table('course_bookings')
    ->whereBetween('created_at', [$monthStart, Carbon::now()])
    ->count();

// 2️⃣ Umsatz
$revenueToday = DB::table('payments')
    ->whereDate('created_at', $today)
    ->whereIn('status', ['paid','completed','success'])
    ->sum('amount');
$revenueMonth = DB::table('payments')
    ->whereBetween('created_at', [$monthStart, Carbon::now()])
    ->whereIn('status', ['paid','completed','success'])
    ->sum('amount');

// 3️⃣ Kurse
$coursesToday = DB::table('courses')->whereDate('created_at', $today)->count();
$coursesMonth = DB::table('courses')
    ->whereBetween('created_at', [$monthStart, Carbon::now()])
    ->count();

// 4️⃣ Kunden
$customersToday = DB::table('ht_customers')->whereDate('created_at', $today)->count();
$customersMonth = DB::table('ht_customers')
    ->whereBetween('created_at', [$monthStart, Carbon::now()])
    ->count();

/* === Zusammenstellung === */
$stats = [
    ['label'=>'Buchungen','icon'=>'fal fa-calendar-check','today'=>$bookingsToday,'month'=>$bookingsMonth],
    ['label'=>'Umsatz','icon'=>'fal fa-coins','today'=>number_format($revenueToday,2,',','.'),'month'=>number_format($revenueMonth,2,',','.')],
    ['label'=>'Kurse','icon'=>'fal fa-chalkboard-teacher','today'=>$coursesToday,'month'=>$coursesMonth],
    ['label'=>'Kunden','icon'=>'fal fa-user-friends','today'=>$customersToday,'month'=>$customersMonth],
];
@endphp

<style>
.greeting-box{background:#fff;border-radius:10px;box-shadow:0 0 6px rgba(0,0,0,0.05);
padding:25px 30px;display:flex;justify-content:space-between;align-items:center;}
.greeting-left{flex:1;}
.greeting-left h4{font-weight:600;font-size:20px;color:#111;margin-bottom:10px;}
.greeting-left .quote-label{font-size:12px;color:#888;}
.greeting-left .quote-text{color:#578E88;font-size:14px;line-height:1.4;margin-top:3px;max-width:380px;}
.greeting-right{display:grid;grid-template-columns:repeat(2,200px);gap:12px;}
.stat-card{background:#F3F3F3;border-radius:6px;display:flex;align-items:center;
padding:8px 12px;transition:all .2s ease;}
.stat-icon{background:#578E88;color:#fff;width:45px;height:45px;border-radius:6px;
display:flex;align-items:center;justify-content:center;font-size:22px;margin-right:10px;}
.stat-info{flex:1;}
.stat-info h6{font-size:13px;font-weight:600;margin:0;}
.stat-info p{font-size:11px;color:#666;margin:0;}
.stat-values{text-align:right;}
.stat-values .today{font-weight:700;font-size:13px;}
.stat-values .month{font-size:11px;color:#578E88;font-weight:600;}
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
    @foreach($stats as $stat)
      <div class="stat-card">
        <div class="stat-icon"><i class="{{ $stat['icon'] }}"></i></div>
        <div class="stat-info">
          <h6>{{ $stat['label'] }}</h6>
          <p>Heute</p>
          <p>Gesamt {{ $monthName }}</p>
        </div>
        <div class="stat-values">
          <div class="today">{{ $stat['today'] }}</div>
          <div class="month">{{ $stat['month'] }}</div>
        </div>
      </div>
    @endforeach
  </div>
</div>

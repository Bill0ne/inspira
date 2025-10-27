@php
    use Illuminate\Support\Facades\Auth;

    $user = Auth::user();
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

    $index = date('z') % count($quotes);
    $quote = $quotes[$index];
@endphp

<div class="card shadow-sm border-0 w-100" style="background:#f9f9f9; border-radius:10px;">
    <div class="card-body py-4 px-5">
        <h4 class="mb-1 fw-semibold">👋 Hallo {{ $user->first_name ?? $user->name }},</h4>
        <p class="text-muted small mb-1">
            🌿 Deine <strong>INSPIRation des Tages</strong>:
        </p>
        <p class="text-success mb-0" style="font-size:14px;">
            {{ $quote }}
        </p>
    </div>
</div>

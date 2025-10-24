<?php
    use Carbon\Carbon;

    $margin = $margin ?? false;
    $now = now();

    // --- Upcoming sessions ---
    $upcomingSessionsQuery = $course->sessions()
        ->where('start_date', '>=', $now)
        ->orderBy('start_date');

    $upcomingSessions = $upcomingSessionsQuery->get();
    $upcomingSessionsCount = $upcomingSessions->count();
    $nextSession = $upcomingSessionsCount ? $upcomingSessions->first() : null;

    // --- Last session ---
    $lastSession = $course->sessions()
        ->where('start_date', '<', $now)
        ->orderByDesc('start_date')
        ->first();

    // --- Helper to format session date range ---
    $formatSessionRange = static function (?object $session, string $dayFmt = 'd.m.Y', string $timeFmt = 'H:i'): ?string {
        if (!$session) return null;

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

    // --- Determine date chip label ---
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

    // === Seats/Progress Logic ===
    // Fully rely on hasAvailableSeats()

    $totalCapacity = 0;
    $totalBooked = 0;
    $hasUnlimited = false;

    // Collect overall capacity info (optional)
    foreach ($upcomingSessions as $session) {
        if (is_null($session->available_seats)) {
            $hasUnlimited = true;
            break;
        }

        // Optional: if you still want to calculate total usage %
        $bookedCount = $session->getBookedCount();
        $totalCapacity += $session->available_seats;
        $totalBooked += $bookedCount;
    }

    // If any session has available seats => not sold out
    $hasAvailableSessions = $upcomingSessions->contains(fn($s) => $s->hasAvailableSeats());
    $isSoldOut = !$hasUnlimited && !$hasAvailableSessions;

    // Optional % indicator
    $percent = $totalCapacity > 0
        ? (int) round(min(100, ($totalBooked / (float)$totalCapacity) * 100))
        : null;

    // --- Seat chip ---
    $showSeatChip  = false;
    $seatChipClass = '';

    if (!is_null($percent)) {
        if ($percent >= 30) {
            $showSeatChip  = true;
            $seatChipClass = 'seat-gray';
            if ($percent >= 60)  $seatChipClass = 'seat-orange';
            if ($percent >= 80)  $seatChipClass = 'seat-red';
        }
    }
?>

<style>
/* ===== Kurskarte – nur für diese Komponente ===== */
.single-services.course-card .services-thumb img{width:100%;height:auto;display:block;}
.single-services.course-card .services-content{display:flex;flex-direction:column;gap:6px;min-height:100%;padding-top:5px;padding-bottom:5px;}
.single-services.course-card .services-content .meta-top{display:flex;align-items:center;gap:8px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-top:0px;margin-bottom:4px;}

/* Basis-Chip (mtxt) */
.single-services.course-card .services-content .meta-top .mtxt{
  background:#F3F3F3 !important;color:#578E88 !important;border-radius:0;padding:8px 14px;display:inline-flex !important;
  align-items:center !important;gap:7px;font-size:13px;font-weight:500;line-height:16px !important;text-decoration:none !important;
  border:0 !important;box-shadow:none !important;background-image:none !important;position:relative;
}
.single-services.course-card .services-content .meta-top .mtxt.missed{ color:#E74C3C !important; }
.single-services.course-card .services-content .meta-top .mtxt::before,
.single-services.course-card .services-content .meta-top .mtxt::after{content:none !important;display:none !important;}

/* Seat-Chip Farbvarianten */
.single-services.course-card .services-content .meta-top .mtxt.seat-gray{ background:#F3F3F3 !important; color:#578E88 !important; }
.single-services.course-card .services-content .meta-top .mtxt.seat-orange{ background:#FFA500 !important; color:#fff !important; }
.single-services.course-card .services-content .meta-top .mtxt.seat-red{ background:#E74C3C !important; color:#fff !important; }

/* Separator-Punkt */
.single-services.course-card .services-content .meta-top .sep{opacity:0.6;display:inline-block;line-height:12px;color:#578E88;}

/* Titel & Beschreibung */
.single-services.course-card h4{margin-top:4px;font-size:16px;line-height:20px;font-weight:600;}
.single-services.course-card .room-item-custom-truncate{margin-bottom:6px;font-size:12px;line-height:18px;color:#333;}

/* Kategorie-Chips unten */
.single-services.course-card .services-content .meta-wrap{margin-top:8px;}
.single-services.course-card .services-content ul.course-meta{display:flex;align-items:center;gap:8px;flex-wrap:nowrap;overflow:hidden;white-space:nowrap;list-style:none;padding:0;margin:0;}
.single-services.course-card .services-content ul.course-meta>li{
  --chip-bg:#F3F3F3;--chip-fg:#578E88;display:inline-flex;align-items:center;background:var(--chip-bg);color:var(--chip-fg);
  font-size:10px;font-weight:500;line-height:12px;padding:8px 14px;border-radius:0;border:0;max-width:100%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;
}
.single-services.course-card .services-content ul.course-meta>li .ctxt{display:inline-block !important;line-height:12px !important;color:#578E88 !important;text-decoration:none !important;background:none !important;border:0 !important;box-shadow:none !important;}
.single-services.course-card .services-content ul.course-meta>li .ctxt::before,
.single-services.course-card .services-content ul.course-meta>li .ctxt::after{content:none !important;display:none !important;}

/* Actions */
.single-services.course-card .services-content .card-actions{display:flex;align-items:center;justify-content:center;gap:2px;margin-top:0;}
.single-services.course-card .services-content .more-link{font-size:12px;font-weight:500;color:#578E88;text-decoration:underline;text-underline-offset:2px;text-decoration-thickness:1px;background:none;border:0;box-shadow:none;display:inline;padding:0 !important;margin:0 !important;line-height:12px !important;}
.single-services.course-card .services-content .more-link:hover{ text-underline-offset:3px; }

/* CTA */
.single-services.course-card .services-content .day-book{margin-top:auto;}
.single-services.course-card .services-content .day-book ul{margin:0;padding:0;list-style:none;}
.single-services.course-card .services-content .day-book li{margin:0;padding:0;}
.single-services.course-card .services-content .day-book .book-button-custom{display:block;width:100%;}
.single-services.course-card .services-content .day-book .book-button-custom.soldout{
  background:#D9D9D9 !important;color:#666 !important;cursor:not-allowed !important;pointer-events:none !important;border-color:#D9D9D9 !important;
}
</style>

<div class="<?php echo \Illuminate\Support\Arr::toCssClasses(['single-services shadow-block mb-30 course-card', 'ser-m' => !$margin]); ?>">
  <div class="services-thumb hover-zoomin wow fadeInUp animated">
    <a href="<?php echo e($course->url); ?>">
      <img src="<?php echo e(RvMedia::getImageUrl($course->thumbnail, 'medium')); ?>" alt="<?php echo e($course->name); ?>">
    </a>
  </div>

  <div class="services-content">

    
    <h4><a href="<?php echo e($course->url); ?>"><?php echo e($course->name); ?></a></h4>
	 
	  <div class="meta-top">
	 
      <?php if($showSeatChip): ?>
        <div class="mtxt <?php echo e($seatChipClass); ?>" title="<?php echo e($percent); ?>%">
          <i class="fal fa-user" aria-hidden="true"></i>
            <?php echo e($totalBooked); ?> / <?php echo e($totalCapacity); ?>

        </div>
      <?php endif; ?>
     </div>
    <?php if($description = $course->description): ?>
      <p class="room-item-custom-truncate" title="<?php echo e($description); ?>">
        <?php echo BaseHelper::clean(Str::limit($description, 120)); ?>

      </p>
    <?php endif; ?>

    
    <div class="meta-top">


      
      <?php if($dateChipLabel): ?>
        <div class="<?php echo e($dateChipClass); ?>" title="<?php echo e($dateChipTitle); ?>">
          <i class="fal fa-calendar-alt" aria-hidden="true"></i>
          <?php echo e($dateChipLabel); ?>

        </div>
      <?php endif; ?>

      
      <?php if($course->price): ?>
        <?php if($dateChipLabel): ?>
          <div class="sep">•</div>
        <?php endif; ?>
        <div class="mtxt"><?php echo e(format_price($course->price)); ?></div>
      <?php endif; ?>
    </div>

    
    <div class="day-book">
      <ul><li>
        <a href="<?php echo e($course->url); ?>"
           class="book-button-custom d-inline-block text-center <?php echo e($isSoldOut ? 'soldout' : ''); ?>"
           style="width:100%;"
           aria-disabled="<?php echo e($isSoldOut ? 'true' : 'false'); ?>"
           data-animation="fadeInRight" data-delay=".8s">
          <?php echo e($isSoldOut ? __('Ausgebucht') : __('Jetzt Buchen')); ?>

        </a>
      </li></ul>
    </div>

    
    <div class="card-actions">
      <a class="more-link" href="<?php echo e($course->url); ?>" aria-label="Mehr Infos zu <?php echo e($course->name); ?>">
        <?php echo e(__('Mehr Infos')); ?>

      </a>
    </div>

  </div>
</div>
<?php /**PATH D:\xampp\htdocs\inspira\platform\themes/riorelax/partials/courses/item.blade.php ENDPATH**/ ?>
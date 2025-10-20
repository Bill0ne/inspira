<?php
    use Carbon\Carbon;

    $margin = $margin ?? false;

    // Nächste Session bestimmen
    $nextSession = $course->isRecurring()
        ? $course->sessions()->where('start_date', '>=', now())->orderBy('start_date')->first()
        : $course->sessions()->orderBy('start_date')->first();

    // Datumsfunktion
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

    // ==== Seats/Progress-Logik ====
    $capacity = $nextSession?->available_seats;           // null = unlimited
    $booked   = $nextSession ? (int) $nextSession->bookings()->count() : 0;
    $percent  = (!is_null($capacity) && (int)$capacity > 0) ? (int) round(min(100, ($booked / (float)$capacity) * 100)) : null;

    // Chip-Regeln: erst ab 30% sichtbar. Farben: >=30 grau, >=60 orange, >=80 rot
    $showSeatChip  = false;
    $seatChipClass = ''; // seat-gray | seat-orange | seat-red

    if (!is_null($percent)) {
        if ($percent >= 30) {
            $showSeatChip  = true;
            $seatChipClass = 'seat-gray';
            if ($percent >= 60)  $seatChipClass = 'seat-orange';
            if ($percent >= 80)  $seatChipClass = 'seat-red';
        }
    }

    // Ausgebucht?
    $isSoldOut = (!is_null($capacity) && $capacity > 0 && $booked >= $capacity);
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
          <?php echo e($booked); ?> / <?php echo e($capacity); ?>

        </div>
      <?php endif; ?>
     </div>
    <?php if($description = $course->description): ?>
      <p class="room-item-custom-truncate" title="<?php echo e($description); ?>">
        <?php echo BaseHelper::clean(Str::limit($description, 120)); ?>

      </p>
    <?php endif; ?>

    
    <div class="meta-top">


      
      <div class="mtxt" title="<?php echo e($dateDisplay ?: __('Kein Termin verfügbar')); ?>">
        <i class="fal fa-calendar-alt" aria-hidden="true"></i>
        <?php echo e($dateDisplay ?: __('Kein Termin verfügbar')); ?>

      </div>

      
      <?php if($course->price): ?>
        <div class="sep">•</div>
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
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/themes/riorelax/partials/courses/item.blade.php ENDPATH**/ ?>
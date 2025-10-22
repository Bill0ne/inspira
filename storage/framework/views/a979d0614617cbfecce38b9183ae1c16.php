<?php
    use Carbon\Carbon;
    Theme::set('pageTitle', $course->name);

    // 24h-Format mit "kein doppeltes Datum am selben Tag"
    $formatRange24h = static function (?string $start, ?string $end, string $dayFmt = 'd.m.Y', string $timeFmt = 'H:i'): ?string {
        if (!$start) return null;
        $s = Carbon::parse($start);
        $e = $end ? Carbon::parse($end) : null;

        if ($e) {
            return $s->isSameDay($e)
                ? $s->format("$dayFmt $timeFmt") . '–' . $e->format($timeFmt)
                : $s->format("$dayFmt $timeFmt") . ' – ' . $e->format("$dayFmt $timeFmt");
        }
        return $s->format("$dayFmt $timeFmt");
    };

    // ---- Seats/Progress-Logik (Detailseite) ----
    // Nächste "relevante" Session bestimmen: bei recurring erste zukünftige, sonst erste
    $nextSession = $course->isRecurring()
        ? $course->sessions()->where('start_date', '>=', now())->orderBy('start_date')->first()
        : $course->sessions()->orderBy('start_date')->first();

    $dateDisplayForNext = $nextSession ? $formatRange24h($nextSession->start_date, $nextSession->end_date) : null;

    // Kapazität + Buchungen der nächsten Session
    $capacityNext = $nextSession?->available_seats;                   // null = unlimited
    $bookedNext   = $nextSession ? (int) $nextSession->bookings()->count() : 0;

    // Prozent nur, wenn Kapazität gesetzt ist
    $percentNext  = (!is_null($capacityNext) && (int)$capacityNext > 0)
        ? (int) round(min(100, ($bookedNext / (float)$capacityNext) * 100))
        : null;

    // Chip-Regeln (wie auf der Karte)
    $showSeatChip  = false;
    $seatChipClass = ''; // seat-gray | seat-orange | seat-red
    if (!is_null($percentNext)) {
        if ($percentNext >= 30) {
            $showSeatChip  = true;
            $seatChipClass = 'seat-gray';
            if ($percentNext >= 60) $seatChipClass = 'seat-orange';
            if ($percentNext >= 80) $seatChipClass = 'seat-red';
        }
    }

    // Button-Status: Single-Session voll? Recurring: alle voll?
    $isSingleSoldOut = false;
    $allSoldOut = false;

    if ($course->isRecurring()) {
        // Wenn ALLE Sessions voll -> Button disable + "Ausgebucht"
        $allSoldOut = $course->sessions->count() > 0
            ? $course->sessions->every(fn($s) => method_exists($s, 'hasAvailableSeats') ? !$s->hasAvailableSeats() : ($s->available_seats !== null && (int)$s->bookings()->count() >= (int)$s->available_seats))
            : false;
    } else {
        // Single: nur die erste/alleinige Session bewerten
        $first = $course->sessions->first();
        if ($first) {
            $cap = $first->available_seats;
            $isSingleSoldOut = (!is_null($cap) && (int)$cap > 0 && (int)$first->bookings()->count() >= (int)$cap);
        }
    }
?>

<style>
/* ===== Nur das Nötigste – Typo bleibt wie im Original ===== */

/* Chips-Row (Dauer, Kategorie, Preis) – wie auf der Karte */
.course-detail-chips{
  display:flex; align-items:center; gap:8px; flex-wrap:wrap;
  margin:6px 0 12px 0;
}
.course-detail-chips .chip{
  background:#F3F3F3; color:#578E88; border-radius:0;
  padding:8px 14px;
  display:inline-flex; align-items:center; gap:7px;
  font-size:13px; font-weight:500; line-height:16px;
  text-decoration:none; border:0; box-shadow:none;
  white-space:nowrap;
}

/* Seat-Chip Farbvarianten (wie Karte) */
.course-detail-chips .chip.seat-gray{ background:#F3F3F3; color:#578E88; }
.course-detail-chips .chip.seat-orange{ background:#FFA500; color:#fff; }
.course-detail-chips .chip.seat-red{ background:#E74C3C; color:#fff; }

/* Select modernisieren (ohne Typo-Änderung am Titel/Body) */
.course-detail-form h4{ margin:8px 0 6px 0; }
.course-detail-select{
  width:100%;
  appearance:none; -webkit-appearance:none; -moz-appearance:none;
  border:1px solid #d1d5db; background:#fff;
  border-radius:6px; padding:10px 12px;
  outline:none; transition:border-color .15s ease, box-shadow .15s ease;
  background-image:none;
}
.course-detail-select:focus{
  border-color:#578E88;
  box-shadow:0 0 0 3px rgba(87,142,136,.15);
}

/* CTA 100% breit (Titel/Beschreibung bleiben unberührt) */
.course-detail-cta{ display:block; width:100%; }
.course-detail-cta.soldout{
  background:#D9D9D9 !important; color:#666 !important;
  cursor:not-allowed !important; pointer-events:none !important;
  border-color:#D9D9D9 !important;
}

/* Container leicht harmonisiert, ohne Schriftgrößen zu ändern */
.course-card-detail{ border-radius:10px; background:#fff; padding:16px; }
.course-card-detail .img-wrap{ margin-bottom:16px; }
.course-description{ margin-top:16px; } /* nur Abstand, keine Typo-Änderung */
</style>

<div class="course-detail-area pt-60 pb-60">
  <div class="container">
    <div class="row">
      
      <div class="col-lg-8 col-md-12">

        
        <?php if($course->thumbnail): ?>
          <div class="img-wrap">
            <img src="<?php echo e(RvMedia::getImageUrl($course->thumbnail, 'large')); ?>"
                 alt="<?php echo e($course->name); ?>" class="img-fluid rounded">
          </div>
        <?php endif; ?>

        
        <div class="course-card-detail shadow-sm mb-5 position-relative">

          
          <h2 class="mb-3"><?php echo e($course->name); ?></h2>

          
          <div class="course-detail-chips">

            
            <?php if($showSeatChip && !is_null($capacityNext)): ?>
              <div class="chip <?php echo e($seatChipClass); ?>" title="<?php echo e($percentNext); ?>%">
                <i class="fal fa-user" aria-hidden="true"></i>
                <?php echo e($bookedNext); ?> / <?php echo e($capacityNext); ?>

              </div>
            <?php endif; ?>

            
            <?php if($dateDisplayForNext): ?>
              <div class="chip" title="<?php echo e($dateDisplayForNext); ?>">
                <i class="fal fa-calendar-alt" aria-hidden="true"></i>
                <?php echo e($dateDisplayForNext); ?>

              </div>
            <?php endif; ?>

            <?php if($course->duration): ?>
              <div class="chip" title="<?php echo e(__('Dauer')); ?>">
                <?php echo e(__('Dauer')); ?>: <?php echo e($course->duration); ?>

              </div>
            <?php endif; ?>

            <?php if($course->category): ?>
              <div class="chip" title="<?php echo e($course->category->name); ?>">
                <?php echo e($course->category->name); ?>

              </div>
            <?php endif; ?>

            <?php if($course->price): ?>
              <div class="chip" title="<?php echo e(format_price($course->price)); ?>">
                <?php echo e(format_price($course->price)); ?>

              </div>
            <?php endif; ?>
          </div>

          
          <?php if($course->sessions->count() > 0): ?>
            <?php if($course->isRecurring()): ?>
              <?php
                  // Button disabled, wenn ALLE Sessions ausgebucht sind
                  $disableRecurringCta = $allSoldOut;
              ?>
              <div class="course-detail-form">
                <h4><?php echo e(__('Termin wählen')); ?></h4>

                <form action="<?php echo e(route('public.course.booking')); ?>" method="POST" class="mb-3">
                  <?php echo csrf_field(); ?>
                  <input type="hidden" name="course_id" value="<?php echo e($course->id); ?>">

                  <select name="session_id" class="course-detail-select mb-2" required <?php echo e($disableRecurringCta ? 'disabled' : ''); ?>>
                    <?php $__currentLoopData = $course->sessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $session): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <?php
                        $label = $formatRange24h($session->start_date, $session->end_date);
                        $cap   = $session->available_seats;
                        $book  = (int) $session->bookings()->count();
                        $full  = (!is_null($cap) && (int)$cap > 0 && $book >= (int)$cap);
                      ?>
                      <option value="<?php echo e($session->id); ?>" <?php echo e($full ? 'disabled' : ''); ?>>
                        <?php echo e($label); ?> <?php echo e($full ? __('– Ausgebucht') : ''); ?>

                      </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </select>

                  
                  <button type="submit"
                          class="btn btn-primary btn-lg course-detail-cta <?php echo e($disableRecurringCta ? 'soldout' : ''); ?>"
                          aria-disabled="<?php echo e($disableRecurringCta ? 'true' : 'false'); ?>">
                    <?php echo e($disableRecurringCta ? __('Ausgebucht') : __('Jetzt Buchen')); ?>

                  </button>
                </form>
              </div>
            <?php else: ?>
              <?php
                $first = $course->sessions->first();
                $singleLabel = $first ? $formatRange24h($first->start_date, $first->end_date) : null;
                $disableSingleCta = $isSingleSoldOut;
              ?>

              
              <?php if($singleLabel): ?>
                <div class="mb-2"><strong><?php echo e(__('Termin')); ?>:</strong> <?php echo e($singleLabel); ?></div>
              <?php endif; ?>

              
              <form action="<?php echo e(route('public.course.booking')); ?>" method="POST" class="mb-3">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="course_id" value="<?php echo e($course->id); ?>">
                <?php if($first): ?>
                  <input type="hidden" name="session_id" value="<?php echo e($first->id); ?>">
                <?php endif; ?>
                <button type="submit"
                        class="btn btn-primary btn-lg course-detail-cta <?php echo e($disableSingleCta ? 'soldout' : ''); ?>"
                        aria-disabled="<?php echo e($disableSingleCta ? 'true' : 'false'); ?>"
                        <?php echo e($disableSingleCta ? 'disabled' : ''); ?>>
                  <?php echo e($disableSingleCta ? __('Ausgebucht') : __('Jetzt Buchen')); ?>

                </button>
              </form>
            <?php endif; ?>
          <?php endif; ?>

          
          <div class="course-description">
            <?php echo BaseHelper::clean($course->description); ?>

          </div>
        </div>

        <?php if(\Botble\Courses\Facades\CourseHelper::isReviewEnabled()): ?>
          <?php echo $__env->make(Theme::getThemeNamespace('views.courses.partials.reviews'), ['model' => $course], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endif; ?>
      </div>

      
      <div class="col-lg-4 col-md-12">
        <?php if($course->instructor): ?>
          <div class="instructor-box shadow-sm p-4 mb-5" style="border-radius:10px; background:#fff;">
            <div class="d-flex align-items-center mb-3">
              <?php if($course->instructor->photo): ?>
                <img src="<?php echo e(RvMedia::getImageUrl($course->instructor->photo, 'thumb')); ?>"
                     alt="<?php echo e($course->instructor->name); ?>"
                     class="rounded-circle me-3" width="80" height="80">
              <?php endif; ?>
              <div>
                <h5 class="mb-1"><?php echo e($course->instructor->name ?? __('No Name')); ?></h5>
                <?php if($course->instructor->email): ?>
                  <p class="mb-1"><i class="fa fa-envelope"></i> <?php echo e($course->instructor->email); ?></p>
                <?php endif; ?>
                <?php if($course->instructor->phone): ?>
                  <p class="mb-1"><i class="fa fa-phone"></i> <?php echo e($course->instructor->phone); ?></p>
                <?php endif; ?>
              </div>
            </div>
            <?php if($course->instructor->bio): ?>
              <div><?php echo BaseHelper::clean($course->instructor->bio); ?></div>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>

    
    <?php if($relatedCourses->isNotEmpty()): ?>
      <div class="related-courses mt-5">
        <h3 class="mb-4"><?php echo e(__('Related Courses')); ?></h3>
        <div class="row">
          <?php $__currentLoopData = $relatedCourses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $related): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-md-6 mb-3">
              <?php echo Theme::partial('courses.item', ['course' => $related]); ?>

            </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/themes/riorelax/views/courses/course.blade.php ENDPATH**/ ?>
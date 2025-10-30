<?php
    use Carbon\Carbon;
    Theme::set('pageTitle', $course->name);

    $now = now();

    // --- Upcoming sessions ---
    $upcomingSessionsQuery = $course->sessions()
        ->where('start_date', '>=', $now)
        ->orderBy('start_date');

    $upcomingSessions = $upcomingSessionsQuery->get();
    $upcomingSessionsCount = $upcomingSessions->count();
    $nextSession = $upcomingSessionsCount ? $upcomingSessions->first() : null;

    // --- Helper for formatting range ---
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

    $dateDisplayForNext = $nextSession ? $formatRange24h($nextSession->start_date, $nextSession->end_date) : null;

    // === Seats/Progress Logic (same as card) ===
    $totalCapacity = 0;
    $totalBooked = 0;
    $hasUnlimited = false;

    foreach ($upcomingSessions as $session) {
        if (is_null($session->available_seats)) {
            $hasUnlimited = true;
            break;
        }

        $bookedCount = $session->getBookedCount();
        $totalCapacity += $session->available_seats;
        $totalBooked += $bookedCount;
    }

    $hasAvailableSessions = $upcomingSessions->contains(fn($s) => $s->hasAvailableSeats());
    $allSoldOut = !$hasUnlimited && !$hasAvailableSessions;

    $percent = $totalCapacity > 0
        ? (int) round(min(100, ($totalBooked / (float)$totalCapacity) * 100))
        : null;

    // --- Seat Chip ---
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

    // --- Button Disable Logic ---
    $isSingleSoldOut = false;
    if (!$course->isRecurring()) {
        $first = $upcomingSessions->first();
        if ($first) {
            $isSingleSoldOut = !$first->hasAvailableSeats();
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
            <?php if($showSeatChip && !is_null($totalCapacity)): ?>
              <div class="chip <?php echo e($seatChipClass); ?>" title="<?php echo e($percent); ?>%">
                <i class="fal fa-user" aria-hidden="true"></i>
                  <?php echo e($totalBooked); ?> / <?php echo e($totalCapacity); ?>

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
                    <?php
                        $basePrice = $course->price;
                        $dynamicPrice = $basePrice;
                        if (is_plugin_active('price-configurator')) {
                        $dynamicPrice = app(\Botble\PriceConfigurator\Services\PriceConfiguratorService::class)
                            ->calculatePrice(
                                $basePrice,
                                \Botble\PriceConfigurator\Enums\TargetTypeEnum::COURSE,
                                $course->id,
                                auth('customer')->user() ?? null
                            );
                        }
                        $priceDifference = $basePrice - $dynamicPrice;
                    ?>

                    <div class="chip price-chip d-flex align-items-center">
                        <?php if($dynamicPrice < $basePrice): ?>
                            <span class="old-price text-decoration-line-through text-muted me-2">
                <?php echo e(format_price($basePrice)); ?>

            </span>
                            <span class="new-price text-success fw-bold">
                <?php echo e(format_price($dynamicPrice)); ?>

            </span>
                        <?php else: ?>
                            <span class="price fw-bold"><?php echo e(format_price($dynamicPrice)); ?></span>
                        <?php endif; ?>
                    </div>

                    <?php if($dynamicPrice < $basePrice): ?>
                        <div class="chip discount-info text-success small mt-1">
                            <i class="fas fa-tag me-1"></i>
                            <?php echo e(__('You save :amount', ['amount' => format_price(abs($priceDifference))])); ?>

                        </div>
                    <?php endif; ?>
                <?php endif; ?>

          </div>
          <?php if($upcomingSessionsCount > 0): ?>
            <?php if($course->isRecurring() || $upcomingSessionsCount > 1): ?>
              <?php
                  $disableRecurringCta = $allSoldOut;
              ?>
              <div class="course-detail-form">
                <h4><?php echo e(__('Termin wählen')); ?></h4>

                <form action="<?php echo e(route('public.course.booking')); ?>" method="POST" class="mb-3">
                  <?php echo csrf_field(); ?>
                  <input type="hidden" name="course_id" value="<?php echo e($course->id); ?>">

                  <select name="session_id" class="course-detail-select mb-2" required <?php echo e($disableRecurringCta ? 'disabled' : ''); ?>>
                    <?php $__currentLoopData = $upcomingSessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $session): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <?php
                        $label = $formatRange24h($session->start_date, $session->end_date);
                        $cap   = $session->available_seats;
                        $book  = (int) $session->getBookedCount();
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
                $first = $upcomingSessions->first();
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
<?php /**PATH D:\xampp\htdocs\inspira\platform\themes/riorelax/views/courses/course.blade.php ENDPATH**/ ?>
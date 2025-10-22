<?php
    $breadcrumbBackgroundImage = Theme::get('breadcrumbBackgroundImage') ?: theme_option('breadcrumb_background_image');
    $bgImage = $breadcrumbBackgroundImage
        ? RvMedia::getImageUrl($breadcrumbBackgroundImage)
        : Theme::asset()->url('images/breadcrumb-bg.jpg');
?>

<style>
/* ===== Compact Breadcrumb (unten mittig, max 250px) ===== */
.breadcrumb-area.breadcrumb--compact{
  position: relative;
  display: flex;
  align-items: flex-end !important;   /* unten ausrichten */
  justify-content: center;
  min-height: 200px !important;
  max-height: 250px !important;

  padding-top: 12px !important;
  padding-bottom: 12px !important;

  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
}

/* Bootstrap-Wrapper neutralisieren & mittig ziehen */
.breadcrumb-area.breadcrumb--compact .container,
.breadcrumb-area.breadcrumb--compact .row,
.breadcrumb-area.breadcrumb--compact [class*="col-"]{
  display: flex;
  align-items: stretch;
  min-height: 0 !important;
  padding: 0 !important;
  margin: 0 !important;
  width: 100%;
}
.breadcrumb-area.breadcrumb--compact .row{ justify-content: center; }

/* Wrapper & Titelblock zentriert unten */
.breadcrumb-area.breadcrumb--compact .breadcrumb-wrap{
  display: flex;
  width: 100%;
  justify-content: center;
}
.breadcrumb-area.breadcrumb--compact .breadcrumb-title{
  position: relative; z-index: 2;
  display: flex; flex-direction: column;
  justify-content: flex-end; align-items: center;
  text-align: center; margin: 0 auto;
  padding-bottom: 12px; /* Abstand unten */
}
/* das innere Wrap um <nav> normal lassen */
.breadcrumb-area.breadcrumb--compact .breadcrumb-title > .breadcrumb-wrap{ display: block; }

/* Typo – 24px/500 (falls 16px gewünscht: 24→16, 28→22) */
.breadcrumb-area.breadcrumb--compact .breadcrumb-title h2{
  font-size: 24px; line-height: 28px; font-weight: 500; margin: 0 0 6px;
}
.breadcrumb-area.breadcrumb--compact .breadcrumb{
  margin: 0; padding: 0; gap: 6px; background: transparent;
  justify-content: center;
}
.breadcrumb-area.breadcrumb--compact .breadcrumb .breadcrumb-item,
.breadcrumb-area.breadcrumb--compact .breadcrumb .breadcrumb-item a{
  font-size: 24px; line-height: 28px; font-weight: 500;
}
.breadcrumb-area.breadcrumb--compact .breadcrumb-item + .breadcrumb-item::before{
  content: "›"; padding: 0 6px; color: rgba(0,0,0,.5);
}

/* Lesbarkeit: leichtes Overlay */
.breadcrumb-area.breadcrumb--compact::before{
  content: ""; position: absolute; inset: 0; z-index: 1;
  background: linear-gradient(to bottom, rgba(0,0,0,.25), rgba(0,0,0,.15));
  pointer-events: none;
}

/* Mobile Feinschliff */
@media (max-width: 575.98px){
  .breadcrumb-area.breadcrumb--compact{
    min-height: 140px !important;
    padding-top: 10px !important;
    padding-bottom: 10px !important;
  }
  .breadcrumb-area.breadcrumb--compact .breadcrumb-title h2,
  .breadcrumb-area.breadcrumb--compact .breadcrumb .breadcrumb-item,
  .breadcrumb-area.breadcrumb--compact .breadcrumb .breadcrumb-item a{
    font-size: 16px; line-height: 22px;
  }
}
</style>

<section class="breadcrumb-area breadcrumb--compact d-flex align-items-center"
         style="background-image:url(<?php echo e($bgImage); ?>);">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-xl-12 col-lg-12">
        <div class="breadcrumb-wrap text-center">
          <div class="breadcrumb-title">
            <?php if($pageTitle = Theme::get('pageTitle')): ?>
              <h2><?php echo BaseHelper::clean($pageTitle); ?></h2>
            <?php endif; ?>

            <?php if($crumbs = Theme::breadcrumb()->getCrumbs()): ?>
              <div class="breadcrumb-wrap">
                <nav aria-label="breadcrumb">
                  <ol class="breadcrumb">
                    <?php $__currentLoopData = $crumbs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $crumb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <?php if(! $loop->last): ?>
                        <li class="breadcrumb-item"><a href="<?php echo e($crumb['url']); ?>"><?php echo e($crumb['label']); ?></a></li>
                      <?php else: ?>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo e($crumb['label']); ?></li>
                      <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </ol>
                </nav>
              </div>
            <?php endif; ?>

          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/themes/riorelax/partials/breadcrumbs.blade.php ENDPATH**/ ?>
<?php if(is_plugin_active('language')): ?>
    <?php
        $supportedLocales = Language::getSupportedLocales();

        if (empty($options)) {
            $options = [
                'before' => '',
                'lang_flag' => true,
                'lang_name' => true,
                'class' => '',
                'after' => '',
            ];
        }
    ?>

    <?php if($supportedLocales && count($supportedLocales) > 1): ?>
        <?php
            $languageDisplay = setting('language_display', 'all');
            $showRelated = setting('language_show_default_item_if_current_version_not_existed', true);
        ?>
        <?php if(setting('language_switcher_display', 'dropdown') == 'dropdown'): ?>
            <div class="dropdown language-switcher d-inline-flex align-items-center">
                <a class="dropdown-toggle" type="button" id="language-switcher-dropdown" data-bs-toggle="dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?php if(Arr::get($options, 'lang_flag', true) && ($languageDisplay == 'all' || $languageDisplay == 'flag')): ?>
                        <?php echo language_flag(Language::getCurrentLocaleFlag()); ?>

                        <span class="ms-1"><?php echo e(Language::getCurrentLocaleName()); ?></span>
                    <?php endif; ?>

                    <?php if(Arr::get($options, 'lang_name', true) && ($languageDisplay == 'name')): ?>
                        &nbsp;<span><?php echo e(Language::getCurrentLocaleName()); ?></span>
                    <?php endif; ?>
                </a>
                <div class="dropdown-menu language-switcher-list" aria-labelledby="language-switcher-dropdown">
                    <?php $__currentLoopData = $supportedLocales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $localeCode => $properties): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($localeCode != Language::getCurrentLocale()): ?>
                            <li>
                                <a class="language-item" href="<?php echo e($showRelated ? Language::getLocalizedURL($localeCode) : url($localeCode)); ?>" target="_self">
                                    <?php if(Arr::get($options, 'lang_flag', true) && ($languageDisplay == 'all' || $languageDisplay == 'flag')): ?>
                                        <?php echo language_flag($properties['lang_flag']); ?> <span><?php echo e($properties['lang_name']); ?></span>
                                    <?php endif; ?>
                                    <?php if(Arr::get($options, 'lang_name', true) &&  ($languageDisplay == 'name')): ?>
                                        &nbsp;<?php echo e($properties['lang_name']); ?>

                                    <?php endif; ?>
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        <?php else: ?>
            <ul class="<?php echo \Illuminate\Support\Arr::toCssClasses(['d-inline-flex align-items-center language-switcher', Arr::get($options, 'class')]); ?>">
                <?php $__currentLoopData = $supportedLocales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $localeCode => $properties): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($localeCode !== Language::getCurrentLocale()): ?>
                        <li class="ms-3">
                            <a href="<?php echo e(Language::getSwitcherUrl($localeCode, $properties['lang_code'])); ?>">
                                <?php if(Arr::get($options, 'lang_flag', true) && ($languageDisplay == 'all' || $languageDisplay == 'flag')): ?>
                                    <?php echo language_flag($properties['lang_flag'], $properties['lang_name']); ?>

                                <?php endif; ?>
                                <?php if(Arr::get($options, 'lang_name', true) && ($languageDisplay == 'all' || $languageDisplay == 'name')): ?>
                                    &nbsp;<span><?php echo e($properties['lang_name']); ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        <?php endif; ?>
    <?php endif; ?>
<?php endif; ?>
<?php /**PATH D:\xampp\htdocs\inspira\platform\themes/riorelax/partials/language-switcher.blade.php ENDPATH**/ ?>
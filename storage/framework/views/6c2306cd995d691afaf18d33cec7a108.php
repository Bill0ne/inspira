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

        <?php $__currentLoopData = $supportedLocales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $localeCode => $properties): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li>
                <a class="<?php echo \Illuminate\Support\Arr::toCssClasses(['language-item d-flex', 'active' => Language::getCurrentLocaleCode() === $localeCode]); ?>" href="<?php echo e($showRelated ? Language::getLocalizedURL($localeCode) : url($localeCode)); ?>" target="_self">
                    <?php if(Arr::get($options, 'lang_flag', true) && ($languageDisplay == 'all' || $languageDisplay == 'flag')): ?>
                        <?php echo language_flag($properties['lang_flag']); ?> <span class="ms-1"><?php echo e($properties['lang_name']); ?></span>
                    <?php endif; ?>
                    <?php if(Arr::get($options, 'lang_name', true) &&  ($languageDisplay == 'name')): ?>
                        &nbsp;<?php echo e($properties['lang_name']); ?>

                    <?php endif; ?>
                </a>
            </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>
<?php endif; ?>

<?php /**PATH /home/u296731902/domains/inspira-zentrum.de/public_html/platform/themes/riorelax/partials/language-switcher-mobile.blade.php ENDPATH**/ ?>
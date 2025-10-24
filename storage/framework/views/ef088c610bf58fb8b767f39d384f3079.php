<?php
    Assets::addScriptsDirectly(config('core.base.general.editor.ckeditor.js'))->addScriptsDirectly('vendor/core/core/base/js/editor.js');

    if (BaseHelper::getRichEditor() == 'ckeditor' && App::getLocale() != 'en') {
        Assets::addScriptsDirectly(sprintf('https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/translations/%s.js', App::getLocale()));
    }

    $attributes['class'] = Arr::get($attributes, 'class', '') . ' form-control editor-ckeditor ays-ignore';
    $attributes['id'] = Arr::get($attributes, 'id', $name);
    $attributes['rows'] = Arr::get($attributes, 'rows', 4);
?>

<?php echo Form::textarea($name, BaseHelper::cleanEditorContent($value), $attributes); ?>

<?php /**PATH D:\xampp\htdocs\inspira\platform/core/base/resources/views/forms/partials/ckeditor.blade.php ENDPATH**/ ?>
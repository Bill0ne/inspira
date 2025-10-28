<?php

namespace Theme\Riorelax\Forms\Fields;

use Botble\Base\Facades\Assets;
use Botble\Base\Forms\FormField;
use Botble\Theme\Facades\Theme;

class ThemeRepeaterField extends FormField
{
    protected function getTemplate(): string
    {
        // Load repeater-field.js from core
        Assets::addScriptsDirectly('vendor/core/core/base/js/repeater-field.js');
        // Optionally, your own CSS or extra script
        Assets::addStylesDirectly('vendor/core/core/base/css/core.css');

        // Point to your custom blade view
        return Theme::getThemeNamespace() . '::partials.forms.fields.theme-repeater-field';
    }
}

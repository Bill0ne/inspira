<?php

namespace Botble\PriceConfigurator\Enums;

use Botble\Base\Facades\Html;
use Botble\Base\Supports\Enum;
use Illuminate\Support\HtmlString;

/**
 * @method static RoundingModeEnum NONE()
 * @method static RoundingModeEnum UP()
 * @method static RoundingModeEnum DOWN()
 * @method static RoundingModeEnum NEAREST()
 */
class RoundingModeEnum extends Enum
{
    public const NONE = 'none';
    public const UP = 'up';
    public const DOWN = 'down';
    public const NEAREST = 'nearest';

    public static $langPath = 'plugins/price-configurator::price-configurator.enums.rounding_mode';

    public function toHtml(): HtmlString|string
    {
        return match ($this->value) {
            self::NONE => Html::tag('span', self::NONE()->label(), ['class' => 'badge bg-secondary text-secondary-fg']),
            self::UP => Html::tag('span', self::UP()->label(), ['class' => 'badge bg-success text-success-fg']),
            self::DOWN => Html::tag('span', self::DOWN()->label(), ['class' => 'badge bg-danger text-danger-fg']),
            self::NEAREST => Html::tag('span', self::NEAREST()->label(), ['class' => 'badge bg-info text-info-fg']),
            default => parent::toHtml(),
        };
    }
}

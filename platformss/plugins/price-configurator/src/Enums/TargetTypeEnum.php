<?php

namespace Botble\PriceConfigurator\Enums;

use Botble\Base\Facades\Html;
use Botble\Base\Supports\Enum;
use Illuminate\Support\HtmlString;

/**
 * @method static TargetTypeEnum ROOM()
 * @method static TargetTypeEnum COURSE()
 */
class TargetTypeEnum extends Enum
{
    public const ROOM = 'room';
    public const COURSE = 'course';

    public static $langPath = 'plugins/price-configurator::price-configurator.enums.target_type';

    public function toHtml(): HtmlString|string
    {
        return match ($this->value) {
            self::ROOM => Html::tag('span', self::ROOM()->label(), ['class' => 'badge bg-primary text-primary-fg']),
            self::COURSE => Html::tag('span', self::COURSE()->label(), ['class' => 'badge bg-success text-success-fg']),
            default => parent::toHtml(),
        };
    }
}

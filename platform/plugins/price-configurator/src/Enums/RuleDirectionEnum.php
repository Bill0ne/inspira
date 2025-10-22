<?php

namespace Botble\PriceConfigurator\Enums;

use Botble\Base\Facades\Html;
use Botble\Base\Supports\Enum;
use Illuminate\Support\HtmlString;

/**
 * @method static RuleDirectionEnum INCREASE()
 * @method static RuleDirectionEnum DECREASE()
 */
class RuleDirectionEnum extends Enum
{
    public const INCREASE = 'increase';
    public const DECREASE = 'decrease';

    public static $langPath = 'plugins/price-configurator::price-configurator.enums.rule_direction';

    public function toHtml(): HtmlString|string
    {
        return match ($this->value) {
            self::INCREASE => Html::tag('span', self::INCREASE()->label(), ['class' => 'badge bg-primary text-primary-fg']),
            self::DECREASE => Html::tag('span', self::DECREASE()->label(), ['class' => 'badge bg-success text-success-fg']),
            default => parent::toHtml(),
        };
    }
}

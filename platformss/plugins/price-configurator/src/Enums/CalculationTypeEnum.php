<?php

namespace Botble\PriceConfigurator\Enums;

use Botble\Base\Facades\Html;
use Botble\Base\Supports\Enum;
use Illuminate\Support\HtmlString;

/**
 * @method static CalculationTypeEnum ABSOLUTE()
 * @method static CalculationTypeEnum PERCENT()
 */
class CalculationTypeEnum extends Enum
{
    public const ABSOLUTE = 'absolute';
    public const PERCENT = 'percent';

    public static $langPath = 'plugins/price-configurator::price-configurator.enums.calculation_type';

    public function toHtml(): HtmlString|string
    {
        return match ($this->value) {
            self::ABSOLUTE => Html::tag('span', self::ABSOLUTE()->label(), ['class' => 'badge bg-primary text-primary-fg']),
            self::PERCENT => Html::tag('span', self::PERCENT()->label(), ['class' => 'badge bg-success text-success-fg']),
            default => parent::toHtml(),
        };
    }
}

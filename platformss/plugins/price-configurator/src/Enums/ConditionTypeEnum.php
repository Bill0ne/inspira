<?php

namespace Botble\PriceConfigurator\Enums;

use Botble\Base\Facades\Html;
use Botble\Base\Supports\Enum;
use Illuminate\Support\HtmlString;

/**
 * @method static ConditionTypeEnum QUANTITY()
 * @method static ConditionTypeEnum AMOUNT()
 */
class ConditionTypeEnum extends Enum
{
    public const QUANTITY = 'quantity';
    public const AMOUNT = 'amount';

    public static $langPath = 'plugins/price-configurator::price-configurator.enums.condition_type';

    public function toHtml(): HtmlString|string
    {
        return match ($this->value) {
            self::QUANTITY => Html::tag('span', self::QUANTITY()->label(), ['class' => 'badge bg-warning text-warning-fg']),
            self::AMOUNT => Html::tag('span', self::AMOUNT()->label(), ['class' => 'badge bg-info text-info-fg']),
            default => parent::toHtml(),
        };
    }
}

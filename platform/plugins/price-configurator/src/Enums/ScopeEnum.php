<?php

namespace Botble\PriceConfigurator\Enums;

use Botble\Base\Facades\Html;
use Botble\Base\Supports\Enum;
use Illuminate\Support\HtmlString;

/**
 * @method static ScopeEnum ALL_PRODUCTS()
 * @method static ScopeEnum BY_CATEGORY()
 * @method static ScopeEnum SPECIFIC_PRODUCTS()
 */
class ScopeEnum extends Enum
{
    public const ALL_PRODUCTS = 'all_products';
    public const BY_CATEGORY = 'by_category';
    public const SPECIFIC_PRODUCTS = 'specific_products';

    public static $langPath = 'plugins/price-configurator::price-configurator.enums.scope';

    public function toHtml(): HtmlString|string
    {
        return match ($this->value) {
            self::ALL_PRODUCTS => Html::tag('span', self::ALL_PRODUCTS()->label(), ['class' => 'badge bg-primary text-primary-fg']),
            self::BY_CATEGORY => Html::tag('span', self::BY_CATEGORY()->label(), ['class' => 'badge bg-info text-info-fg']),
            self::SPECIFIC_PRODUCTS => Html::tag('span', self::SPECIFIC_PRODUCTS()->label(), ['class' => 'badge bg-warning text-warning-fg']),
            default => parent::toHtml(),
        };
    }
}

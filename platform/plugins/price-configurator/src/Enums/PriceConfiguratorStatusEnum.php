<?php

namespace Botble\PriceConfigurator\Enums;

use Botble\Base\Facades\Html;
use Botble\Base\Supports\Enum;
use Illuminate\Support\HtmlString;

/**
 * @method static PriceConfiguratorStatusEnum ACTIVE()
 * @method static PriceConfiguratorStatusEnum INACTIVE()
 */
class PriceConfiguratorStatusEnum extends Enum
{
    public const ACTIVE = 'active';
    public const INACTIVE = 'inactive';

    public static $langPath = 'plugins/price-configurator::price-configurator.enums.statuses';

    public function toHtml(): HtmlString|string
    {
        return match ($this->value) {
            self::ACTIVE => Html::tag('span', self::ACTIVE()->label(), ['class' => 'badge bg-success text-success-fg']),
            self::INACTIVE => Html::tag('span', self::INACTIVE()->label(), ['class' => 'badge bg-danger text-danger-fg']),
            default => parent::toHtml(),
        };
    }
}

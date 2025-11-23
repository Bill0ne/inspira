<?php

namespace Botble\Hotel\Enums;

use Botble\Base\Enums\Enum;
use Botble\Base\Facades\BaseHelper;

/**
 * @method static CustomerCardCoverageType NONE()
 * @method static CustomerCardCoverageType PARTIAL()
 * @method static CustomerCardCoverageType FULL()
 */
class CustomerCardCoverageType extends Enum
{
    public const NONE = 'none';
    public const PARTIAL = 'partial';
    public const FULL = 'full';

    public static $langPath = 'plugins/hotel::customer-card.coverage_type';

    public function label(): string
    {
        return match ($this->value) {
            self::FULL => BaseHelper::clean(__('Full coverage')),
            self::PARTIAL => BaseHelper::clean(__('Partial coverage')),
            default => BaseHelper::clean(__('No coverage')),
        };
    }
}

<?php

namespace Botble\Hotel\Enums;

use Botble\Base\Supports\Enum;

/**
 * @method static CustomerCardTypeEnum FIVE()
 * @method static CustomerCardTypeEnum TEN()
 * @method static CustomerCardTypeEnum CUSTOM()
 */
class CustomerCardTypeEnum extends Enum
{
    public const FIVE = '5er';

    public const TEN = '10er';

    public const CUSTOM = 'custom';

    public static $langPath = 'plugins/hotel::customer-card.types';
}

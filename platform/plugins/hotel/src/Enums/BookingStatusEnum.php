<?php

namespace Botble\Hotel\Enums;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Supports\Enum;
use Illuminate\Support\HtmlString;

/**
 * @method static BookingStatusEnum PENDING()
 * @method static BookingStatusEnum PROCESSING()
 * @method static BookingStatusEnum COMPLETED()
 * @method static BookingStatusEnum CANCELLED()
 * @method static BookingStatusEnum FAILED()
 * @method static BookingStatusEnum AWAITING_PAYMENT()
 */
class BookingStatusEnum extends Enum
{
    public const PENDING = 'pending';

    public const PROCESSING = 'processing';

    public const COMPLETED = 'completed';

    public const CANCELLED = 'cancelled';

    public const FAILED = 'failed';

    public const AWAITING_PAYMENT = 'awaiting_payment';

    public static $langPath = 'plugins/hotel::booking.statuses';

    public function toHtml(): HtmlString|string|null
    {
        $color = match ($this->value) {
            self::PENDING => 'warning',
            self::PROCESSING => 'info',
            self::COMPLETED => 'success',
            self::CANCELLED => 'danger',
            self::FAILED => 'warning',
            self::AWAITING_PAYMENT => 'primary',
            default => 'primary',
        };

        return BaseHelper::renderBadge($this->label(), $color);
    }
}

<?php

namespace Botble\InspiraCancellation\Enums;

use Botble\Base\Facades\Html;
use Botble\Base\Supports\Enum;
use Illuminate\Support\HtmlString;

/**
 * @method static CancellationStatusEnum PENDING()
 * @method static CancellationStatusEnum PENDING_REFUND()
 * @method static CancellationStatusEnum APPROVED()
 * @method static CancellationStatusEnum PAID()
 * @method static CancellationStatusEnum REJECTED()
 */
class CancellationStatusEnum extends Enum
{
    public const PENDING = 'pending';

    public const PENDING_REFUND = 'pending_refund';

    public const APPROVED = 'approved';

    public const PAID = 'paid';

    public const REJECTED = 'rejected';

    public static $langPath = 'plugins/inspira-cancellation::cancellation.statuses';

    public function toHtml(): HtmlString|string
    {
        return match ($this->value) {
            self::PENDING => Html::tag('span', self::PENDING()->label(), ['class' => 'badge bg-secondary text-secondary-fg']),
            self::PENDING_REFUND => Html::tag('span', self::PENDING_REFUND()->label(), ['class' => 'badge bg-warning text-warning-fg']),
            self::APPROVED => Html::tag('span', self::APPROVED()->label(), ['class' => 'badge bg-info text-info-fg']),
            self::PAID => Html::tag('span', self::PAID()->label(), ['class' => 'badge bg-success text-success-fg']),
            self::REJECTED => Html::tag('span', self::REJECTED()->label(), ['class' => 'badge bg-danger text-danger-fg']),
            default => parent::toHtml(),
        };
    }
}

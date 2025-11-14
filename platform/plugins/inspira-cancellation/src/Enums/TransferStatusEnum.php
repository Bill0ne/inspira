<?php

namespace Botble\InspiraCancellation\Enums;

use Botble\Base\Facades\Html;
use Botble\Base\Supports\Enum;
use Illuminate\Support\HtmlString;

/**
 * @method static TransferStatusEnum PENDING()
 * @method static TransferStatusEnum APPROVED()
 * @method static TransferStatusEnum REJECTED()
 */
class TransferStatusEnum extends Enum
{
    public const PENDING = 'pending';

    public const APPROVED = 'approved';

    public const REJECTED = 'rejected';

    public static $langPath = 'plugins/inspira-cancellation::cancellation.transfer_statuses';

    public function toHtml(): HtmlString|string
    {
        return match ($this->value) {
            self::PENDING => Html::tag('span', self::PENDING()->label(), ['class' => 'badge bg-warning text-warning-fg']),
            self::APPROVED => Html::tag('span', self::APPROVED()->label(), ['class' => 'badge bg-success text-success-fg']),
            self::REJECTED => Html::tag('span', self::REJECTED()->label(), ['class' => 'badge bg-danger text-danger-fg']),
            default => parent::toHtml(),
        };
    }
}

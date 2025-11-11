<?php

namespace Botble\InspiraCancellation\Models;

use Botble\Base\Models\BaseModel;

class CancellationRule extends BaseModel
{
    protected $table = 'insp_cancellation_rules';

    protected $fillable = [
        'type',
        'from_days',
        'to_days',
        'refund_percent',
        'description',
        'active',
        'order',
    ];

    protected $casts = [
        'from_days' => 'int',
        'to_days' => 'int',
        'refund_percent' => 'int',
        'active' => 'bool',
        'order' => 'int',
    ];
}

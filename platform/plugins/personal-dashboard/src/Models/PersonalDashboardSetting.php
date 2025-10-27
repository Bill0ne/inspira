<?php

namespace Botble\PersonalDashboard\Models;

use Botble\Base\Models\BaseModel;

class PersonalDashboardSetting extends BaseModel
{
    protected $table = 'personal_dashboard_settings';

    protected $fillable = [
        'key',
        'value',
    ];

    protected $casts = [
        'value' => 'array',
    ];
}

<?php

namespace Botble\PriceConfigurator\Models;

use Botble\Base\Models\BaseModel;
use Botble\PriceConfigurator\Enums\PriceConfiguratorStatusEnum;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tier extends BaseModel
{
    protected $table = 'pconf_tiers';

    protected $fillable = [
        'name',
        'priority',
        'is_exclusive',
        'starts_at',
        'ends_at',
        'notes',
        'status',
    ];

    protected $casts = [
        'status' => PriceConfiguratorStatusEnum::class,
        'is_exclusive' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function rules(): HasMany
    {
        return $this->hasMany(Rule::class, 'price_tier_id');
    }
}

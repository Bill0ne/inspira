<?php

namespace Botble\PriceConfigurator\Models;

use Botble\Base\Models\BaseModel;
use Botble\PriceConfigurator\Enums\CalculationTypeEnum;
use Botble\PriceConfigurator\Enums\PriceConfiguratorStatusEnum;
use Botble\PriceConfigurator\Enums\RoundingModeEnum;
use Botble\PriceConfigurator\Enums\ScopeEnum;
use Botble\PriceConfigurator\Enums\TargetTypeEnum;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rule extends BaseModel
{
    protected $table = 'pconf_rules';

    protected $fillable = [
        'price_tier_id',
        'customer_category_id',
        'scope',
        'target_type',
        'target_ids',
        'calculation_type',
        'calculation_value',
        'rounding_mode',
        'round_to',
        'status',
    ];

    protected $casts = [
        'target_ids' => 'array',
        'status' => PriceConfiguratorStatusEnum::class,
        'target_type' => TargetTypeEnum::class,
        'scope' => ScopeEnum::class,
        'calculation_type' => CalculationTypeEnum::class,
        'rounding_mode' => RoundingModeEnum::class,
    ];

    public function tier(): BelongsTo
    {
        return $this->belongsTo(Tier::class, 'price_tier_id');
    }

    public function customerCategory(): BelongsTo
    {
        return $this->belongsTo(CustomerCategory::class, 'customer_category_id');
    }

}

<?php

namespace Botble\PriceConfigurator\Models;

use Botble\Base\Models\BaseModel;
use Botble\PriceConfigurator\Enums\CalculationTypeEnum;
use Botble\PriceConfigurator\Enums\ConditionTypeEnum;
use Botble\PriceConfigurator\Enums\PriceConfiguratorStatusEnum;

class QuantityDiscount extends BaseModel
{
    protected $table = 'pconf_quantity_discounts';

    protected $fillable = [
        'title',
        'condition_type',
        'range_min',
        'range_max',
        'discount_type',
        'discount_value',
        'priority',
        'status',
    ];

    protected $casts = [
        'status' => PriceConfiguratorStatusEnum::class,
        'condition_type' => ConditionTypeEnum::class,
        'discount_type' => CalculationTypeEnum::class,
        'range_min' => 'integer',
        'range_max' => 'integer',
        'discount_value' => 'decimal:2',
    ];
}

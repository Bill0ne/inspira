<?php

namespace Botble\PriceConfigurator\Models;

use Illuminate\Database\Eloquent\Model;

class QuantityDiscount extends Model
{
    protected $table = 'pc_quantity_discounts';
    protected $fillable = [
        'title','condition_type','range_min','range_max',
        'discount_type','discount_value','apply_to','priority','status'
    ];
}

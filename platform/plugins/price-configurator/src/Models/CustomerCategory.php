<?php

namespace Botble\PriceConfigurator\Models;

use Botble\Base\Models\BaseModel;
use Botble\PriceConfigurator\Enums\PriceConfiguratorStatusEnum;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Botble\Hotel\Models\Customer;

class CustomerCategory extends BaseModel
{
    protected $table = 'pconf_customer_categories';

    protected $fillable = [
        'code',
        'label',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => PriceConfiguratorStatusEnum::class,
    ];

    public function rules(): HasMany
    {
        return $this->hasMany(Rule::class, 'customer_category_id');
    }

    public function customers()
    {
        return $this->hasMany(Customer::class, 'customer_category_id');
    }

}

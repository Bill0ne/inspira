<?php

namespace Botble\Community\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Botble\Hotel\Models\Customer;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunityMember extends BaseModel
{
    protected $table = 'community_members';

    protected $fillable = [
        'customer_id',
        'name',
        'photo',
        'quote',
        'short_description',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => BaseStatusEnum::class,
        'name' => SafeContent::class,
        'quote' => SafeContent::class,
        'short_description' => SafeContent::class,
        'description' => SafeContent::class,
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id')->withDefault();
    }
}

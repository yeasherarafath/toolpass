<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\CouponUsage;
use App\Models\Order;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'coupons';

    protected $fillable = [
        'code',
        'type',
        'value',
        'min_amount',
        'max_discount',
        'currency',
        'starts_at',
        'ends_at',
        'usage_limit',
        'used_count',
        'status',
    ];

    protected $casts = [

    ];

    public function couponUsages(): HasMany
    {
        return $this->hasMany(CouponUsage::class, 'coupon_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'coupon_id');
    }
}

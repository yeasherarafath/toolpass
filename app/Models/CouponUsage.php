<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\User;

class CouponUsage extends Model
{
    use SoftDeletes;

    protected $table = 'coupon_usages';

    protected $fillable = [
        'coupon_id',
        'order_id',
        'user_id',
        'discount_amount',
    ];

    protected $casts = [

    ];

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

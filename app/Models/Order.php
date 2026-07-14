<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use App\Models\Package;
use App\Models\Coupon;
use App\Models\AdminTask;
use App\Models\CouponUsage;
use App\Models\OrderCustomFieldValue;
use App\Models\OtpRequest;
use App\Models\Payment;
use App\Models\SupportTicket;
use App\Models\UserToolAccess;

class Order extends Model
{
    use SoftDeletes;

    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'package_id',
        'order_number',
        'amount',
        'discount_amount',
        'payable_amount',
        'currency',
        'wallet_amount',
        'paid_via_wallet',
        'is_trial',
        'payment_method',
        'payment_status',
        'order_status',
        'required_info_status',
        'required_info_submitted_at',
        'required_info_reviewed_by',
        'required_info_reviewed_at',
        'required_info_reject_reason',
        'starts_at',
        'expires_at',
        'renewed_from_order_id',
        'customer_note',
        'admin_note',
        'converted_from_trial_order_id',
        'coupon_id',
        'coupon_code',
    ];

    protected $casts = [

    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function requiredInfoReviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'required_info_reviewed_by');
    }

    public function renewedFromOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'renewed_from_order_id');
    }

    public function convertedFromTrialOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'converted_from_trial_order_id');
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }

    public function adminTasks(): HasMany
    {
        return $this->hasMany(AdminTask::class, 'order_id');
    }

    public function couponUsages(): HasMany
    {
        return $this->hasMany(CouponUsage::class, 'order_id');
    }

    public function orderCustomFieldValues(): HasMany
    {
        return $this->hasMany(OrderCustomFieldValue::class, 'order_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'converted_from_trial_order_id');
    }

    public function otpRequests(): HasMany
    {
        return $this->hasMany(OtpRequest::class, 'order_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'order_id');
    }

    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class, 'order_id');
    }

    public function userToolAccesses(): HasMany
    {
        return $this->hasMany(UserToolAccess::class, 'order_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\ActivityLog;
use App\Models\AdminTask;
use App\Models\CouponUsage;
use App\Models\DeviceResetRequest;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OtpRequest;
use App\Models\Payment;
use App\Models\Review;
use App\Models\SupportTicket;
use App\Models\UserToolAccess;
use App\Models\UserToolDevice;
use App\Models\Wallet;

class User extends Authenticatable
{
    use SoftDeletes;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'status',
        'avatar',
        'last_login_at',
        'email_verified_at',
        'notes',
        'remember_token',
    ];

    protected $casts = [

    ];
    protected $hidden = ['password', 'remember_token'];

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class, 'user_id');
    }

    public function adminTasks(): HasMany
    {
        return $this->hasMany(AdminTask::class, 'user_id');
    }

    public function couponUsages(): HasMany
    {
        return $this->hasMany(CouponUsage::class, 'user_id');
    }

    public function deviceResetRequests(): HasMany
    {
        return $this->hasMany(DeviceResetRequest::class, 'user_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function otpRequests(): HasMany
    {
        return $this->hasMany(OtpRequest::class, 'user_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'user_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'user_id');
    }

    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class, 'user_id');
    }

    public function userToolAccesses(): HasMany
    {
        return $this->hasMany(UserToolAccess::class, 'user_id');
    }

    public function userToolDevices(): HasMany
    {
        return $this->hasMany(UserToolDevice::class, 'user_id');
    }

    public function wallets(): HasMany
    {
        return $this->hasMany(Wallet::class, 'user_id');
    }
}

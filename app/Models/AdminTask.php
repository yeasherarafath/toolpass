<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use App\Models\Order;
use App\Models\UserToolAccess;
use App\Models\OtpRequest;
use App\Models\DeviceResetRequest;

class AdminTask extends Model
{
    use SoftDeletes;

    protected $table = 'admin_tasks';

    protected $fillable = [
        'user_id',
        'order_id',
        'user_tool_access_id',
        'otp_request_id',
        'device_reset_request_id',
        'title',
        'description',
        'type',
        'priority',
        'status',
        'assigned_to',
        'due_at',
        'completed_at',
    ];

    protected $casts = [

    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function userToolAccess(): BelongsTo
    {
        return $this->belongsTo(UserToolAccess::class, 'user_tool_access_id');
    }

    public function otpRequest(): BelongsTo
    {
        return $this->belongsTo(OtpRequest::class, 'otp_request_id');
    }

    public function deviceResetRequest(): BelongsTo
    {
        return $this->belongsTo(DeviceResetRequest::class, 'device_reset_request_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}

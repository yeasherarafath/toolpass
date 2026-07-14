<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use App\Models\Order;
use App\Models\Tool;
use App\Models\ToolAccount;
use App\Models\AdminTask;
use App\Models\DeviceResetRequest;
use App\Models\OtpRequest;
use App\Models\SupportTicket;
use App\Models\UserToolDevice;

class UserToolAccess extends Model
{
    use SoftDeletes;

    protected $table = 'user_tool_accesses';

    protected $fillable = [
        'user_id',
        'order_id',
        'tool_id',
        'tool_account_id',
        'source',
        'status',
        'delivery_status',
        'customer_email_for_invite',
        'starts_at',
        'expires_at',
        'invited_at',
        'delivered_at',
        'access_note',
        'delivery_note',
        'internal_note',
        'last_accessed_at',
        'revoked_at',
        'revoked_by',
        'revoked_reason',
        'created_by',
        'updated_by',
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

    public function tool(): BelongsTo
    {
        return $this->belongsTo(Tool::class, 'tool_id');
    }

    public function toolAccount(): BelongsTo
    {
        return $this->belongsTo(ToolAccount::class, 'tool_account_id');
    }

    public function revokedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revoked_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function adminTasks(): HasMany
    {
        return $this->hasMany(AdminTask::class, 'user_tool_access_id');
    }

    public function deviceResetRequests(): HasMany
    {
        return $this->hasMany(DeviceResetRequest::class, 'user_tool_access_id');
    }

    public function otpRequests(): HasMany
    {
        return $this->hasMany(OtpRequest::class, 'user_tool_access_id');
    }

    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class, 'user_tool_access_id');
    }

    public function userToolDevices(): HasMany
    {
        return $this->hasMany(UserToolDevice::class, 'user_tool_access_id');
    }
}

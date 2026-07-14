<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Tool;
use App\Models\User;
use App\Models\DeviceResetRequest;
use App\Models\OtpRequest;
use App\Models\SupportTicket;
use App\Models\UserToolAccess;
use App\Models\UserToolDevice;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ToolAccount extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'tool_accounts';

    protected $fillable = [
        'tool_id',
        'name',
        'login_email',
        'login_password_encrypted',
        'recovery_email',
        'account_url',
        'subscription_type',
        'purchase_price',
        'renewal_price',
        'purchase_date',
        'renewal_date',
        'expires_at',
        'max_users',
        'used_slots',
        'device_restriction_enabled',
        'max_devices',
        'used_devices',
        'device_limit_type',
        'allow_device_reset',
        'device_reset_interval_days',
        'device_policy_note',
        'otp_required',
        'otp_type',
        'otp_receiver',
        'otp_note',
        'two_factor_secret_encrypted',
        'backup_codes_encrypted',
        'status',
        'last_checked_at',
        'last_issue_at',
        'issue_note',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [

    ];

    public function tool(): BelongsTo
    {
        return $this->belongsTo(Tool::class, 'tool_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deviceResetRequests(): HasMany
    {
        return $this->hasMany(DeviceResetRequest::class, 'tool_account_id');
    }

    public function otpRequests(): HasMany
    {
        return $this->hasMany(OtpRequest::class, 'tool_account_id');
    }

    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class, 'tool_account_id');
    }

    public function userToolAccesses(): HasMany
    {
        return $this->hasMany(UserToolAccess::class, 'tool_account_id');
    }

    public function userToolDevices(): HasMany
    {
        return $this->hasMany(UserToolDevice::class, 'tool_account_id');
    }
}

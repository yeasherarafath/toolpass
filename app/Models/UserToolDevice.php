<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use App\Models\UserToolAccess;
use App\Models\Tool;
use App\Models\ToolAccount;
use App\Models\DeviceResetRequest;

class UserToolDevice extends Model
{
    use SoftDeletes;

    protected $table = 'user_tool_devices';

    protected $fillable = [
        'user_id',
        'user_tool_access_id',
        'tool_id',
        'tool_account_id',
        'device_name',
        'device_type',
        'browser_name',
        'operating_system',
        'ip_address',
        'user_agent',
        'device_fingerprint',
        'status',
        'approved_by',
        'approved_at',
        'first_used_at',
        'last_used_at',
        'removed_by',
        'removed_at',
        'remove_reason',
        'note',
    ];

    protected $casts = [

    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function userToolAccess(): BelongsTo
    {
        return $this->belongsTo(UserToolAccess::class, 'user_tool_access_id');
    }

    public function tool(): BelongsTo
    {
        return $this->belongsTo(Tool::class, 'tool_id');
    }

    public function toolAccount(): BelongsTo
    {
        return $this->belongsTo(ToolAccount::class, 'tool_account_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function removedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'removed_by');
    }

    public function deviceResetRequests(): HasMany
    {
        return $this->hasMany(DeviceResetRequest::class, 'old_device_id');
    }
}

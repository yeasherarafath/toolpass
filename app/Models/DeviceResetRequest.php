<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\UserToolAccess;
use App\Models\Tool;
use App\Models\ToolAccount;
use App\Models\UserToolDevice;
use App\Models\AdminTask;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeviceResetRequest extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'device_reset_requests';

    protected $fillable = [
        'user_id',
        'user_tool_access_id',
        'tool_id',
        'tool_account_id',
        'old_device_id',
        'status',
        'customer_reason',
        'admin_note',
        'reviewed_by',
        'reviewed_at',
        'reject_reason',
        'completed_at',
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

    public function oldDevice(): BelongsTo
    {
        return $this->belongsTo(UserToolDevice::class, 'old_device_id');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function adminTasks(): HasMany
    {
        return $this->hasMany(AdminTask::class, 'device_reset_request_id');
    }
}

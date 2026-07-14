<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Order;
use App\Models\UserToolAccess;
use App\Models\Tool;
use App\Models\ToolAccount;
use App\Models\AdminTask;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OtpRequest extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'otp_requests';

    protected $fillable = [
        'user_id',
        'order_id',
        'user_tool_access_id',
        'tool_id',
        'tool_account_id',
        'request_type',
        'status',
        'customer_message',
        'admin_note',
        'otp_code_encrypted',
        'otp_expires_at',
        'requested_for_at',
        'provided_by',
        'provided_at',
        'used_at',
        'rejected_by',
        'rejected_at',
        'reject_reason',
        'cancelled_by',
        'cancelled_at',
        'cancel_reason',
        'revoked_by',
        'revoked_at',
        'revoke_reason',
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

    public function tool(): BelongsTo
    {
        return $this->belongsTo(Tool::class, 'tool_id');
    }

    public function toolAccount(): BelongsTo
    {
        return $this->belongsTo(ToolAccount::class, 'tool_account_id');
    }

    public function providedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'provided_by');
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function revokedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revoked_by');
    }

    public function adminTasks(): HasMany
    {
        return $this->hasMany(AdminTask::class, 'otp_request_id');
    }
}

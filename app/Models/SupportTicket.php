<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Order;
use App\Models\Tool;
use App\Models\ToolAccount;
use App\Models\UserToolAccess;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportTicket extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'support_tickets';

    protected $fillable = [
        'user_id',
        'order_id',
        'tool_id',
        'tool_account_id',
        'user_tool_access_id',
        'ticket_number',
        'subject',
        'category',
        'priority',
        'status',
        'assigned_to',
        'last_reply_at',
        'closed_at',
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

    public function userToolAccess(): BelongsTo
    {
        return $this->belongsTo(UserToolAccess::class, 'user_tool_access_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(SupportTicketMessage::class, 'ticket_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}

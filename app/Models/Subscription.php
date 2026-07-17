<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Owner;
use App\Models\Plan;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $connection = 'central';

    protected $table = 'subscriptions';

    protected $fillable = [
        'owner_id',
        'tenant_id',
        'plan_id',
        'status',
        'starts_at',
        'ends_at',
        'amount',
        'currency',
        'note',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active'
            && (is_null($this->ends_at) || $this->ends_at->isFuture());
    }
}

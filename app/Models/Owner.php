<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\ModelCache;
use App\Models\Tenant;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Owner extends Authenticatable
{
    use HasFactory;
    use SoftDeletes;
    use Notifiable;
    use HasRoles;
    use ModelCache;

    protected $connection = 'central';

    protected $guard_name = 'owner';

    protected $table = 'owners';

    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'phone',
        'password',
        'business_name',
        'status',
        'last_login_at',
        'email_verified_at',
        'notes',
        'remember_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'last_login_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'owner_id');
    }

    public function activeSubscription()
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->latest('starts_at')
            ->first();
    }
}

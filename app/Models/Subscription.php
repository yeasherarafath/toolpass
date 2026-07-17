<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Owner;
use App\Models\Plan;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Subscription model (central DB).
 *
 * IMPORTANT — KEEP PLAN & SUBSCRIPTION IN SYNC:
 * Each plan limit/quota/feature is mirrored here as a nullable "*_override" column so a
 * platform admin can manually enforce/override limits for a single subscription:
 *   - override IS NULL  -> inherit the value from the related Plan
 *   - override HAS value -> use the admin-set value for THIS subscription only
 * Manual usage/enforcement is handled by: emails_used, sms_used, usage_reset_at,
 * quota_enforced, is_suspended, suspend_reason.
 *
 * If a new limit/quota/feature column is added to the `plans` table (see App\Models\Plan),
 * you MUST also:
 *   1. Add a matching nullable `<col>_override` column to this `subscriptions` table
 *      (database/migrations/2026_07_17_160000_create_central_saas_tables.php).
 *   2. Add it to $fillable and $casts below.
 *   3. Add a resolver method that uses effectiveLimit($this-><col>_override, '<col>').
 */
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
        'max_staff_override',
        'max_customers_override',
        'max_packages_override',
        'email_quota_override',
        'sms_quota_override',
        'feature_overrides',
        'emails_used',
        'sms_used',
        'usage_reset_at',
        'quota_enforced',
        'is_suspended',
        'suspend_reason',
        'note',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'amount' => 'decimal:2',
        'max_staff_override' => 'integer',
        'max_customers_override' => 'integer',
        'max_packages_override' => 'integer',
        'email_quota_override' => 'integer',
        'sms_quota_override' => 'integer',
        'feature_overrides' => 'array',
        'emails_used' => 'integer',
        'sms_used' => 'integer',
        'usage_reset_at' => 'datetime',
        'quota_enforced' => 'boolean',
        'is_suspended' => 'boolean',
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
        return ! $this->is_suspended
            && $this->status === 'active'
            && (is_null($this->ends_at) || $this->ends_at->isFuture());
    }

    protected function effectiveLimit(?int $override, string $planColumn): ?int
    {
        if (! is_null($override)) {
            return $override;
        }

        return $this->plan?->{$planColumn};
    }

    public function maxStaff(): ?int
    {
        return $this->effectiveLimit($this->max_staff_override, 'max_staff');
    }

    public function maxCustomers(): ?int
    {
        return $this->effectiveLimit($this->max_customers_override, 'max_customers');
    }

    public function maxPackages(): ?int
    {
        return $this->effectiveLimit($this->max_packages_override, 'max_packages');
    }

    public function emailQuota(): ?int
    {
        return $this->effectiveLimit($this->email_quota_override, 'email_quota');
    }

    public function smsQuota(): ?int
    {
        return $this->effectiveLimit($this->sms_quota_override, 'sms_quota');
    }

    public function features(): array
    {
        return $this->feature_overrides ?? $this->plan?->features ?? [];
    }

    public function withinEmailQuota(int $additional = 1): bool
    {
        if (! $this->quota_enforced) {
            return true;
        }

        $quota = $this->emailQuota();

        if (is_null($quota)) {
            return true;
        }

        return ($this->emails_used + $additional) <= $quota;
    }

    public function withinSmsQuota(int $additional = 1): bool
    {
        if (! $this->quota_enforced) {
            return true;
        }

        $quota = $this->smsQuota();

        if (is_null($quota)) {
            return true;
        }

        return ($this->sms_used + $additional) <= $quota;
    }

    public function resetUsage(): void
    {
        $this->forceFill([
            'emails_used' => 0,
            'sms_used' => 0,
            'usage_reset_at' => now(),
        ])->save();
    }
}

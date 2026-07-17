<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\ModelCache;

/**
 * Plan model (central DB).
 *
 * IMPORTANT — KEEP PLAN & SUBSCRIPTION IN SYNC:
 * This table holds the BASELINE limits/quotas for a plan (max_staff, max_customers,
 * max_packages, email_quota, sms_quota, features, ...). The Subscription table mirrors
 * each of these as a nullable "*_override" column so a platform admin can manually
 * enforce/override the limit for a single subscription (null override = inherit from plan).
 *
 * If you add ANY new limit/quota/feature column to this `plans` table, you MUST also:
 *   1. Add a matching nullable `<col>_override` column to the `subscriptions` table
 *      (see database/migrations/2026_07_17_160000_create_central_saas_tables.php).
 *   2. Add it to Subscription::$fillable and $casts.
 *   3. Add an effectiveLimit()-style accessor on the Subscription model so the
 *      override -> plan fallback resolution keeps working.
 * Non-limit columns (name, price, description, etc.) do NOT need a subscription mirror.
 */
class Plan extends Model
{
    use HasFactory;
    use SoftDeletes;
    use ModelCache;

    protected $connection = 'central';

    protected $table = 'plans';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'currency',
        'billing_cycle',
        'max_staff',
        'max_customers',
        'max_packages',
        'email_quota',
        'sms_quota',
        'features',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'features' => 'array',
    ];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'plan_id');
    }
}

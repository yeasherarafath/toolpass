<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Per-tenant (per-business) key-value setting.
 *
 * Lives in the TENANT database (uses the default connection which stancl/tenancy
 * swaps at runtime). For GLOBAL platform settings use App\Models\PlatformSetting.
 * Read/write through App\Services\TenantSettings for caching + encryption.
 */
class Setting extends Model
{
    use HasFactory;

    protected $table = 'settings';

    protected $fillable = [
        'key',
        'value',
        'is_encrypted',
        'group',
    ];

    protected $casts = [
        'is_encrypted' => 'boolean',
    ];
}

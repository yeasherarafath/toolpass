<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\DeviceResetRequest;
use App\Models\OtpRequest;
use App\Models\PackageTool;
use App\Models\SupportTicket;
use App\Models\ToolAccount;
use App\Models\UserToolAccess;
use App\Models\UserToolDevice;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tool extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'tools';

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'website_url',
        'logo',
        'description',
        'status',
        'access_type',
        'otp_required',
        'otp_type',
        'otp_note',
        'device_restriction_enabled',
        'device_limit_type',
        'default_max_devices',
        'device_policy_note',
    ];

    protected $casts = [

    ];

    public function deviceResetRequests(): HasMany
    {
        return $this->hasMany(DeviceResetRequest::class, 'tool_id');
    }

    public function otpRequests(): HasMany
    {
        return $this->hasMany(OtpRequest::class, 'tool_id');
    }

    public function packageTools(): HasMany
    {
        return $this->hasMany(PackageTool::class, 'tool_id');
    }

    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class, 'tool_id');
    }

    public function toolAccounts(): HasMany
    {
        return $this->hasMany(ToolAccount::class, 'tool_id');
    }

    public function userToolAccesses(): HasMany
    {
        return $this->hasMany(UserToolAccess::class, 'tool_id');
    }

    public function userToolDevices(): HasMany
    {
        return $this->hasMany(UserToolDevice::class, 'tool_id');
    }
}

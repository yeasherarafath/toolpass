<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UsageCounter extends Model
{
    use HasFactory;

    protected $connection = 'central';

    protected $table = 'usage_counters';

    protected $fillable = [
        'tenant_id',
        'period',
        'emails_sent',
        'sms_sent',
    ];
}

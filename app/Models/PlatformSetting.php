<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlatformSetting extends Model
{
    use HasFactory;

    protected $connection = 'central';

    protected $table = 'platform_settings';

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

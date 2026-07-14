<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NotificationTemplate extends Model
{
    use SoftDeletes;

    protected $table = 'notification_templates';

    protected $fillable = [
        'slug',
        'channel',
        'subject',
        'body',
        'variables',
        'status',
    ];

    protected $casts = [
        'variables' => 'array',
    ];

}

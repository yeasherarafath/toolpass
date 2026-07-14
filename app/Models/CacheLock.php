<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CacheLock extends Model
{
    use SoftDeletes;

    protected $table = 'cache_locks';

    protected $fillable = [
        'key',
        'owner',
        'expiration',
    ];

    protected $casts = [

    ];

}

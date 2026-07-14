<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Translation extends Model
{
    use HasFactory;

    protected $table = 'translations';

    protected $fillable = [
        'subject_type',
        'subject_id',
        'locale',
        'field',
        'value',
    ];

    protected $casts = [

    ];

}

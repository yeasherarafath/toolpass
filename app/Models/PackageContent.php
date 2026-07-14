<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Package;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PackageContent extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'package_contents';

    protected $fillable = [
        'package_id',
        'section',
        'title',
        'body',
        'sort_order',
        'status',
    ];

    protected $casts = [

    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class, 'package_id');
    }
}

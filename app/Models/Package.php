<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Order;
use App\Models\PackageContent;
use App\Models\PackageCustomField;
use App\Models\PackageTool;
use App\Models\Review;

class Package extends Model
{
    use SoftDeletes;

    protected $table = 'packages';

    protected $fillable = [
        'name',
        'slug',
        'type',
        'delivery_type',
        'description',
        'meta_title',
        'meta_description',
        'price',
        'currency',
        'duration_days',
        'status',
        'sort_order',
        'is_featured',
        'is_trial',
        'trial_days',
    ];

    protected $casts = [

    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'package_id');
    }

    public function packageContents(): HasMany
    {
        return $this->hasMany(PackageContent::class, 'package_id');
    }

    public function packageCustomFields(): HasMany
    {
        return $this->hasMany(PackageCustomField::class, 'package_id');
    }

    public function packageTools(): HasMany
    {
        return $this->hasMany(PackageTool::class, 'package_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'package_id');
    }
}

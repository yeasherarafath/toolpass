<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Package;
use App\Models\OrderCustomFieldValue;

class PackageCustomField extends Model
{
    use SoftDeletes;

    protected $table = 'package_custom_fields';

    protected $fillable = [
        'package_id',
        'label',
        'name',
        'type',
        'placeholder',
        'help_text',
        'options',
        'is_required',
        'validation_rules',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function orderCustomFieldValues(): HasMany
    {
        return $this->hasMany(OrderCustomFieldValue::class, 'package_custom_field_id');
    }
}

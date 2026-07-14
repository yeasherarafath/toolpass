<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Order;
use App\Models\PackageCustomField;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderCustomFieldValue extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'order_custom_field_values';

    protected $fillable = [
        'order_id',
        'package_custom_field_id',
        'field_name',
        'field_label',
        'value',
        'file_path',
    ];

    protected $casts = [

    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function packageCustomField(): BelongsTo
    {
        return $this->belongsTo(PackageCustomField::class, 'package_custom_field_id');
    }
}

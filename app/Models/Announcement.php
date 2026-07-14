<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Announcement extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'announcements';

    protected $fillable = [
        'title',
        'message',
        'type',
        'status',
        'visible_to',
        'starts_at',
        'ends_at',
        'created_by',
    ];

    protected $casts = [

    ];

    public function visibleTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'visible_to');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

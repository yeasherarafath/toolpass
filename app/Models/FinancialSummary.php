<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinancialSummary extends Model
{
    use HasFactory;

    protected $table = 'financial_summaries';

    protected $fillable = [
        'summary_date',
        'currency',
        'orders_count',
        'gross_revenue',
        'discounts',
        'refunds',
        'wallet_used',
        'net_revenue',
    ];

    protected $casts = [

    ];

}

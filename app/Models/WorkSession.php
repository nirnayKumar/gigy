<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_id',
        'earnings_entry_id',
        'start_time',
        'end_time',
        'total_hours',
        'deliveries_completed',
        'gross_earnings',
        'net_earnings',
        'hourly_rate',
        'city',
        'worker_role',
        'is_demo',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'total_hours' => 'decimal:2',
        'deliveries_completed' => 'integer',
        'gross_earnings' => 'decimal:2',
        'net_earnings' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'is_demo' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function earningsEntry(): BelongsTo
    {
        return $this->belongsTo(EarningsEntry::class);
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class);
    }
}

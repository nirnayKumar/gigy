<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EarningsGoal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'target_amount',
        'start_date',
        'end_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate achieved amount for this goal based on user's net earnings during goal period.
     */
    public function getAchievedAmountAttribute(): float
    {
        return (float) $this->user->earningsEntries()
            ->whereBetween('work_date', [$this->start_date, $this->end_date])
            ->get()
            ->sum('net_earnings');
    }

    /**
     * Calculate percentage progress towards target.
     */
    public function getProgressPercentAttribute(): float
    {
        if ($this->target_amount <= 0) {
            return 0.00;
        }

        $achieved = $this->achieved_amount;
        return round(min(100.0, ($achieved / (float) $this->target_amount) * 100), 1);
    }

    /**
     * Calculate remaining amount needed.
     */
    public function getRemainingAmountAttribute(): float
    {
        $achieved = $this->achieved_amount;
        return max(0.00, round((float) $this->target_amount - $achieved, 2));
    }
}

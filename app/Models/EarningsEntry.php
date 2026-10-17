<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EarningsEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'work_date',
        'gross_earnings',
        'incentives',
        'fuel_expenses',
        'other_expenses',
        'platform_deductions',
        'hours_worked',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'work_date' => 'date',
            'gross_earnings' => 'decimal:2',
            'incentives' => 'decimal:2',
            'fuel_expenses' => 'decimal:2',
            'other_expenses' => 'decimal:2',
            'platform_deductions' => 'decimal:2',
            'hours_worked' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function complaints(): HasMany
    {
        return $this->hasMany(Complaint::class);
    }

    /**
     * Total income = gross earnings + incentives
     */
    public function getTotalIncomeAttribute(): float
    {
        return round((float) $this->gross_earnings + (float) $this->incentives, 2);
    }

    /**
     * Total expenses = fuel expenses + other expenses + platform deductions
     */
    public function getTotalExpensesAttribute(): float
    {
        return round((float) $this->fuel_expenses + (float) $this->other_expenses + (float) $this->platform_deductions, 2);
    }

    /**
     * Net earnings = total income - total expenses
     */
    public function getNetEarningsAttribute(): float
    {
        return round($this->total_income - $this->total_expenses, 2);
    }

    /**
     * Effective hourly earnings = net earnings / hours worked (if hours worked > 0)
     */
    public function getEffectiveHourlyEarningsAttribute(): float
    {
        $hours = (float) $this->hours_worked;
        if ($hours > 0) {
            return round($this->net_earnings / $hours, 2);
        }
        return 0.00;
    }
}

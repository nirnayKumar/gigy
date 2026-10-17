<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'earnings_entry_id',
        'company_name',
        'issue_category',
        'expected_amount',
        'received_amount',
        'incident_date',
        'description',
        'preferred_language',
        'generated_draft',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'expected_amount' => 'decimal:2',
            'received_amount' => 'decimal:2',
            'incident_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function earningsEntry(): BelongsTo
    {
        return $this->belongsTo(EarningsEntry::class);
    }
}

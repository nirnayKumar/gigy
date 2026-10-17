<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_session_id',
        'user_id',
        'company_id',
        'delivery_identifier',
        'pickup_time',
        'dropoff_time',
        'duration_minutes',
        'distance_km',
        'earnings',
        'tip_amount',
        'status',
        'city',
        'is_demo',
    ];

    protected $casts = [
        'pickup_time' => 'datetime',
        'dropoff_time' => 'datetime',
        'duration_minutes' => 'integer',
        'distance_km' => 'decimal:2',
        'earnings' => 'decimal:2',
        'tip_amount' => 'decimal:2',
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

    public function workSession(): BelongsTo
    {
        return $this->belongsTo(WorkSession::class);
    }
}

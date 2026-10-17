<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'category',
        'color',
        'is_demo',
    ];

    protected $casts = [
        'is_demo' => 'boolean',
    ];

    public function workSessions(): HasMany
    {
        return $this->hasMany(WorkSession::class);
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class);
    }
}

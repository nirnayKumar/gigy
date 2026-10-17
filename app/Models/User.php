<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'worker_type',
        'preferred_language',
        'city',
        'is_demo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_demo' => 'boolean',
        ];
    }

    public function earningsEntries(): HasMany
    {
        return $this->hasMany(EarningsEntry::class)->orderBy('work_date', 'desc');
    }

    public function complaints(): HasMany
    {
        return $this->hasMany(Complaint::class)->orderBy('created_at', 'desc');
    }

    public function assistantConversations(): HasMany
    {
        return $this->hasMany(AssistantConversation::class)->orderBy('updated_at', 'desc');
    }

    public function workSessions(): HasMany
    {
        return $this->hasMany(WorkSession::class)->orderBy('start_time', 'desc');
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class)->orderBy('pickup_time', 'desc');
    }

    public function earningsGoals(): HasMany
    {
        return $this->hasMany(EarningsGoal::class)->orderBy('start_date', 'desc');
    }
}

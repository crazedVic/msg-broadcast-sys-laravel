<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Broadcast extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'content',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'content' => 'string',
    ];

    /**
     * The users that belong to the broadcast.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'broadcast_user_states');
    }

    /**
     * Get the user states for the broadcast.
     */
    public function userStates()
    {
        return $this->hasMany(BroadcastUserState::class);
    }

    public function getUserStateClassAttribute()
    {
        $hasDeletedUserState = $this->userStates()->withTrashed()->whereNotNull('deleted_at')->exists();
        $hasActiveUserState = $this->userStates()->exists();
    
        return match (true) {
            // Broadcast is deleted and has no user state
            $this->trashed() && !$hasActiveUserState && !$hasDeletedUserState => 'text-orange-500 font-bold',
    
            // Broadcast is deleted and user state is deleted
            $this->trashed() && $hasDeletedUserState => 'text-red-500',
    
            // Broadcast is deleted but has active user state
            $this->trashed() && $hasActiveUserState => 'text-orange-500',
    
            // No user state (new broadcast)
            !$hasActiveUserState => 'font-semibold',
    
            // Has user state but not deleted (read)
            default => 'font-normal',
        };
    }
}
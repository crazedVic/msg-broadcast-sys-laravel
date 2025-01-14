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
    public function userState()
    {
        return $this->hasOne(BroadcastUserState::class);
    }

    /**
     * Get the user state class for the broadcast.
     */
    public function getUserStateClassAttribute()
    {
    if ($this->trashed()) {
        return $this->userState?->deleted_at 
            ? 'text-red-500'    // Deleted
            : 'text-orange-500'; // Archived
    }

     // Handle the userState when the broadcast is not soft-deleted
     if ($this->userState?->deleted_at) {
        return 'text-red-500'; // Deleted
    }
    
    return (!$this->userState || is_null($this->userState->read_at))
        ? 'font-semibold'  // New 
        : 'font-normal';   // Read
    }
}
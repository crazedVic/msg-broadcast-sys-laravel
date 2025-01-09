<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Broadcast extends Model
{
    use HasFactory;

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
}
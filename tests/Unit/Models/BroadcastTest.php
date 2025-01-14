<?php

use App\Models\Broadcast;
use App\Models\BroadcastUserState;
use App\Models\User;

it('returns text-orange-500 when broadcast is trashed and has no user state', function () {
    $broadcast = Broadcast::factory()->create();
    $broadcast->delete();
    expect($broadcast->user_state_class)->toBe('text-orange-500');
});

it('returns text-red-500 when broadcast is trashed and user state is deleted', function () {
    // Create a broadcast and a user
    $broadcast = Broadcast::factory()->create();
    $user = User::factory()->create();

    // Create a user state and associate it with the broadcast
    $broadcastUserState = BroadcastUserState::factory()->make([
        'user_id' => $user->id,
        'read_at' => now(),
    ]);
    $broadcast->userState()->save($broadcastUserState);

    // Soft delete the user state and broadcast
    $broadcastUserState->delete();
    $broadcast->delete();

    // Assert that the user state class is 'text-red-500'
    expect($broadcast->user_state_class)->toBe('text-red-500');
});

it('returns text-orange-500 when broadcast is trashed but was previously read', function () {
    $broadcast = Broadcast::factory()->create();
    $user = User::factory()->create();
    $broadcastUserState = BroadcastUserState::factory()->make([
        'user_id' => $user->id,
    ]);
    $broadcast->userState()->save($broadcastUserState);
    $broadcast->delete();
    expect($broadcast->user_state_class)->toBe('text-orange-500');
});

it('returns font-semibold when there is no user state (new broadcast)', function () {
    $broadcast = Broadcast::factory()->create();
    expect($broadcast->user_state_class)->toBe('font-semibold');
});

it('returns font-normal when there is a user state and it is not deleted but read', function () {
    $broadcast = Broadcast::factory()->create();
    $user = User::factory()->create();
    $broadcastUserState = BroadcastUserState::factory()->make([
        'user_id' => $user->id,
        'read_at' => now(),
    ]);
    $broadcast->userState()->save($broadcastUserState);
    expect($broadcast->user_state_class)->toBe('font-normal');
});

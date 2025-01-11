<?php

use App\Models\Broadcast;
use App\Models\BroadcastUserState;
use App\Models\User;

it('returns text-orange-500 font-bold when broadcast is trashed and has no user state', function () {
    $broadcast = Broadcast::factory()->trashed()->make();
    expect($broadcast->user_state_class)->toBe('text-orange-500 font-bold');
});

it('returns text-red-500 when broadcast is trashed and user state is deleted', function () {
    $broadcast = Broadcast::factory()->trashed()->make();
    $broadcast->is_deleted = true; // Simulate user state being deleted
    expect($broadcast->user_state_class)->toBe('text-red-500');
});

it('returns text-orange-500 when broadcast is trashed but was previously read', function () {
    $broadcast = Broadcast::factory()->trashed()->make();
    $broadcast->has_state = true; // Simulate previous read state
    expect($broadcast->user_state_class)->toBe('text-orange-500');
});

it('returns font-semibold when there is no user state (new broadcast)', function () {
    $broadcast = Broadcast::factory()->make();
    expect($broadcast->user_state_class)->toBe('font-semibold');
});

it('returns font-normal when there is a user state and it is not deleted', function () {
    $broadcast = Broadcast::factory()->make();
    $broadcast->has_state = true; // Simulate existing user state
    expect($broadcast->user_state_class)->toBe('font-normal');
});
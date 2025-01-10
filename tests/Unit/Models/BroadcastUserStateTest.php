<?php

use App\Models\Broadcast;
use App\Models\BroadcastUserState;
use App\Models\User;

it('can create a broadcast user state', function () {
    $state = BroadcastUserState::factory()->create();
    expect($state)->toBeInstanceOf(BroadcastUserState::class);
});

it('belongs to a broadcast', function () {
    $state = BroadcastUserState::factory()
        ->for(Broadcast::factory())
        ->create();
        
    expect($state->broadcast)->toBeInstanceOf(Broadcast::class);
});

it('belongs to a user', function () {
    $state = BroadcastUserState::factory()
        ->for(User::factory())
        ->create();
        
    expect($state->user)->toBeInstanceOf(User::class);
});

it('has required attributes', function () {
    $state = BroadcastUserState::factory()->create();
    
    expect($state->status)->toBeString();
    expect($state->read_at)->toBeNull();
});

it('requires status', function () {
    $state = new BroadcastUserState();
    
    $this->assertFalse($state->isValid());
    $this->assertArrayHasKey('status', $state->errors());
});
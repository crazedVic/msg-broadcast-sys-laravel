<?php

use App\Models\Broadcast;
use App\Models\User;
use App\Models\BroadcastUserState;

test('broadcast has many users through broadcast user states', function () {
    $broadcast = Broadcast::factory()->create();
    BroadcastUserState::factory()->count(3)->create(['broadcast_id' => $broadcast->id]);

    expect($broadcast->users)->toHaveCount(3);
});

test('broadcast user state belongs to a broadcast', function () {
    $broadcast = Broadcast::factory()->create();
    $broadcastUserState = BroadcastUserState::factory()->create(['broadcast_id' => $broadcast->id]);

    expect($broadcastUserState->broadcast)->toBeInstanceOf(Broadcast::class);
});

test('broadcast user state belongs to a user', function () {
    $user = User::factory()->create();
    $broadcastUserState = BroadcastUserState::factory()->create(['user_id' => $user->id]);

    expect($broadcastUserState->user)->toBeInstanceOf(User::class);
});

test('index requires authentication', function () {
    $response = $this->get(route('user.broadcasts.index'));
    $response->assertRedirect(route('login'));
});

test('authenticated user can view broadcasts', function () {
    $user = User::factory()->create();
    $broadcasts = Broadcast::factory()->count(3)->create();
    
    $response = $this->actingAs($user)
        ->get(route('user.broadcasts.index'));
        
    $response->assertOk();
    $response->assertViewHas('broadcasts');
    $response->assertSee($broadcasts->first()->title);
});

test('show requires authentication', function () {
    $broadcast = Broadcast::factory()->create();
    
    $response = $this->get(route('user.broadcasts.show', $broadcast));
    $response->assertRedirect(route('login'));
});

test('authenticated user can view a broadcast', function () {
    $user = User::factory()->create();
    $broadcast = Broadcast::factory()->create();
    
    $response = $this->actingAs($user)
        ->get(route('user.broadcasts.show', $broadcast));
        
    $response->assertOk();
    $response->assertViewHas('broadcast');
    $response->assertSee($broadcast->title);
});

test('soft delete requires authentication', function () {
    $broadcast = Broadcast::factory()->create();
    
    $response = $this->delete(route('user.broadcasts.soft-delete', $broadcast));
    $response->assertRedirect(route('login'));
});

test('authenticated user can soft delete their own broadcast user state', function () {
    $user = User::factory()->create();
    $broadcast = Broadcast::factory()->create();
    $state = BroadcastUserState::factory()->create([
        'user_id' => $user->id,
        'broadcast_id' => $broadcast->id,
    ]);

    $response = $this->actingAs($user)
        ->delete(route('user.broadcasts.soft-delete', $broadcast->id));

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Broadcast soft deleted successfully.');
    $this->assertSoftDeleted('broadcast_user_states', ['id' => $state->id]);
    $this->assertDatabaseHas('broadcasts', [
        'id' => $broadcast->id,
        'deleted_at' => null,
    ]);
});

test('cannot view non-existent broadcast', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)
        ->get(route('user.broadcasts.show', 999));
        
    $response->assertNotFound();
});

test('cannot soft delete non-existent broadcast', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)
        ->delete(route('user.broadcasts.soft-delete', 999));
        
    $response->assertNotFound();
});

test('api index requires authentication', function () {
    $response = $this->getJson('/api/broadcasts');
    $response->assertUnauthorized();
});

test('authenticated user can get broadcasts via api', function () {
    $user = User::factory()->create();
    $broadcasts = Broadcast::factory()->count(3)->create();

    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/broadcasts');

    $response->assertOk()
        ->assertJsonStructure([[
            'id',
            'title',
            'content',
            'created_at',
            'is_read',
            'is_deleted'
        ]])
        ->assertHeader('recordCount', 3);
});

test('api creates broadcast user state for new broadcasts', function () {
    $user = User::factory()->create();
    $broadcast = Broadcast::factory()->create();

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/broadcasts');

    $this->assertDatabaseHas('broadcast_user_states', [
        'user_id' => $user->id,
        'broadcast_id' => $broadcast->id
    ]);
});

test('api only returns unread or new broadcasts', function () {
    $user = User::factory()->create();
    
    // Create different broadcast states
    $newBroadcast = Broadcast::factory()->create();
    $readBroadcast = Broadcast::factory()->create();
    BroadcastUserState::factory()->create([
        'user_id' => $user->id,
        'broadcast_id' => $readBroadcast->id,
        'read_at' => now()
    ]);
    $deletedBroadcast = Broadcast::factory()->create();
    BroadcastUserState::factory()->create([
        'user_id' => $user->id,
        'broadcast_id' => $deletedBroadcast->id,
        'deleted_at' => now()
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/broadcasts');

    $response->assertJsonCount(1)
        ->assertJsonFragment(['id' => $newBroadcast->id]);
});

test('api update state requires authentication', function () {
    $broadcast = Broadcast::factory()->create();
    $response = $this->putJson("/api/broadcasts/{$broadcast->id}/state");
    $response->assertUnauthorized();
});

test('api can mark broadcast as read', function () {
    $user = User::factory()->create();
    $broadcast = Broadcast::factory()->create();
    $state = BroadcastUserState::factory()->create([
        'user_id' => $user->id,
        'broadcast_id' => $broadcast->id
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->putJson("/api/broadcasts/{$broadcast->id}/state", [
            'action' => 'read'
        ]);

    $response->assertOk()
        ->assertJson(['message' => 'Broadcast marked as read']);
    
    $this->assertNotNull($state->fresh()->read_at);
});

test('api can mark broadcast as read and deleted', function () {
    $user = User::factory()->create();
    $broadcast = Broadcast::factory()->create();
    $state = BroadcastUserState::factory()->create([
        'user_id' => $user->id,
        'broadcast_id' => $broadcast->id
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->putJson("/api/broadcasts/{$broadcast->id}/state", [
            'action' => 'delete'
        ]);

    $response->assertOk()
        ->assertJson(['message' => 'Broadcast marked as read & deleted']);
    
    $this->assertNotNull($state->fresh()->read_at);
    $this->assertNotNull($state->fresh()->deleted_at);
});

test('api update state requires valid action', function () {
    $user = User::factory()->create();
    $broadcast = Broadcast::factory()->create();

    $response = $this->actingAs($user, 'sanctum')
        ->putJson("/api/broadcasts/{$broadcast->id}/state", [
            'action' => 'invalid'
        ]);

    $response->assertStatus(400)
        ->assertJson(['error' => 'Invalid action']);
});

test('api update state creates state if not exists', function () {
    $user = User::factory()->create();
    $broadcast = Broadcast::factory()->create();

    $response = $this->actingAs($user, 'sanctum')
        ->putJson("/api/broadcasts/{$broadcast->id}/state", [
            'action' => 'read'
        ]);

    $response->assertOk();
    $this->assertDatabaseHas('broadcast_user_states', [
        'user_id' => $user->id,
        'broadcast_id' => $broadcast->id,
        'read_at' => now()
    ]);
});
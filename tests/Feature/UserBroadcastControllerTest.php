<?php

use App\Models\Broadcast;
use App\Models\User;
use App\Models\BroadcastUserState;

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
    $user = User::factory()->create(); // Create a regular user
    $broadcast = Broadcast::factory()->create(); // Create a broadcast
    $state = BroadcastUserState::factory()->create([
        'user_id' => $user->id,
        'broadcast_id' => $broadcast->id,
    ]); // Create the associated BroadcastUserState for the user

    $response = $this->actingAs($user)
        ->delete(route('user.broadcasts.soft-delete', $broadcast->id));

    // Ensure the response redirects back with a success message
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Broadcast soft deleted successfully.');

    // Assert the BroadcastUserState is soft deleted
    $this->assertSoftDeleted('broadcast_user_states', ['id' => $state->id]);

    // Ensure the broadcast itself remains untouched
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
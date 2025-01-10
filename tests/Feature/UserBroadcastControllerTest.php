<?php

use App\Models\Broadcast;
use App\Models\User;

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

test('authenticated user can soft delete a broadcast', function () {
    $user = User::factory()->create();
    $broadcast = Broadcast::factory()->create();
    
    $response = $this->actingAs($user)
        ->delete(route('user.broadcasts.soft-delete', $broadcast));
        
    $response->assertRedirect(route('user.broadcasts.index'));
    $this->assertSoftDeleted($broadcast);
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
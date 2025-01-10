<?php

use App\Models\Broadcast;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['is_admin' => true]);
    $this->user = User::factory()->create();
});

test('admin routes require authentication', function () {
    $response = $this->get(route('admin.broadcasts.index'));
    $response->assertRedirect(route('login'));
});

test('non-admin cannot access admin routes', function () {
    $response = $this->actingAs($this->user)
        ->get(route('admin.broadcasts.index'));
        
    $response->assertForbidden();
});

test('admin can view broadcasts index', function () {
    $broadcasts = Broadcast::factory()->count(3)->create();
    
    $response = $this->actingAs($this->admin)
        ->get(route('admin.broadcasts.index'));
        
    $response->assertOk();
    $response->assertViewHas('broadcasts');
    $response->assertSee($broadcasts->first()->title);
});

test('admin can view create form', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('admin.broadcasts.create'));
        
    $response->assertOk();
    $response->assertViewIs('admin.broadcasts.create');
});

test('admin can store a new broadcast', function () {
    $data = [
        'title' => 'Test Broadcast',
        'content' => 'This is a test broadcast content'
    ];
    
    $response = $this->actingAs($this->admin)
        ->post(route('admin.broadcasts.store'), $data);
        
    $response->assertRedirect(route('admin.broadcasts.index'));
    $this->assertDatabaseHas('broadcasts', $data);
});

test('admin can view a broadcast', function () {
    $broadcast = Broadcast::factory()->create();
    
    $response = $this->actingAs($this->admin)
        ->get(route('admin.broadcasts.show', $broadcast));
        
    $response->assertOk();
    $response->assertViewHas('broadcast');
    $response->assertSee($broadcast->title);
});

test('admin can view edit form', function () {
    $broadcast = Broadcast::factory()->create();
    
    $response = $this->actingAs($this->admin)
        ->get(route('admin.broadcasts.edit', $broadcast));
        
    $response->assertOk();
    $response->assertViewHas('broadcast');
    $response->assertSee($broadcast->title);
});

test('admin can update a broadcast', function () {
    $broadcast = Broadcast::factory()->create();
    $data = [
        'title' => 'Updated Title',
        'content' => 'Updated content'
    ];
    
    $response = $this->actingAs($this->admin)
        ->put(route('admin.broadcasts.update', $broadcast), $data);
        
    $response->assertRedirect(route('admin.broadcasts.index'));
    $this->assertDatabaseHas('broadcasts', array_merge(['id' => $broadcast->id], $data));
});

test('admin can delete a broadcast', function () {
    $broadcast = Broadcast::factory()->create();
    
    $response = $this->actingAs($this->admin)
        ->delete(route('admin.broadcasts.destroy', $broadcast));
        
    $response->assertRedirect(route('admin.broadcasts.index'));

    // Assert the broadcast is soft deleted
    $this->assertSoftDeleted('broadcasts', ['id' => $broadcast->id]);
});

test('store requires valid data', function () {
    $response = $this->actingAs($this->admin)
        ->post(route('admin.broadcasts.store'), []);
        
    $response->assertSessionHasErrors(['title', 'content']);
});

test('update requires valid data', function () {
    $broadcast = Broadcast::factory()->create();
    
    $response = $this->actingAs($this->admin)
        ->put(route('admin.broadcasts.update', $broadcast), []);
        
    $response->assertSessionHasErrors(['title', 'content']);
});
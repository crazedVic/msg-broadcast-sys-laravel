<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Broadcast;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BroadcastTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_broadcast()
    {
        $broadcast = Broadcast::factory()->create();
        $this->assertInstanceOf(Broadcast::class, $broadcast);
    }

    /** @test */
    public function it_has_required_attributes()
    {
        $broadcast = Broadcast::factory()->create();
        
        $this->assertIsString($broadcast->title);
        $this->assertIsString($broadcast->content);
    }

    /** @test */
    public function it_has_users_relationship()
    {
        $broadcast = Broadcast::factory()
            ->has(User::factory()->count(3))
            ->create();
            
        $this->assertCount(3, $broadcast->users);
        $this->assertInstanceOf(User::class, $broadcast->users->first());
    }

    /** @test */
    public function it_has_user_states_relationship()
    {
        $broadcast = Broadcast::factory()->create();
        
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $broadcast->userStates());
    }

    /** @test */
    public function it_has_user_state_class_accessor()
    {
        $broadcast = Broadcast::factory()->create();
        
        $this->assertIsString($broadcast->user_state_class);
    }

    /** @test */
    public function it_requires_title_and_content()
    {
        $broadcast = new Broadcast();
        
        $validator = Validator::make($broadcast->toArray(), [
            'title' => 'required',
            'content' => 'required',
        ]);
        
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('title', $validator->errors()->toArray());
        $this->assertArrayHasKey('content', $validator->errors()->toArray());
    }
}
<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Passport\Passport;



class ShowTest extends TestCase
{
    use RefreshDatabase;


    public function test_show_returns_authenticated_user_details()
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Passport::actingAs($user);

        $response = $this->getJson('/api/show');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'id',
            'name',
            'email',
            'created_at',
            'updated_at',
        ]);

        $response->assertJson([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }

    public function testShowUserUnauthenticated()
    {
        $response = $this->getJson('/api/show');
    
        $response->assertStatus(401);
    }
}

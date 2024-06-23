<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Game;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Passport;
use Spatie\Permission\Models\Role;

class GameTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $regularUser;

    public function setUp(): void
    {
        parent::setUp();

        // Crear roles y usuarios con Spatie Laravel Permission
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'user']);

        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole('admin');

        $this->regularUser = User::factory()->create();
        $this->regularUser->assignRole('user');
    }

    public function testPlayGameSuccess()
    {
        Passport::actingAs($this->regularUser);
    
        $response = $this->postJson("/api/players/{$this->regularUser->id}/games");
    
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
            'game' => [
                'user_id',
                'die1',
                'die2',
                'win',
            ]
        ]);
    }

    public function testPlayGameValidationFailure()
    {
        $user = User::factory()->create();
        Passport::actingAs($this->adminUser);

        $response = $this->postJson("/api/players/{$user->id}/games");

        $response->assertStatus(403);
        $response->assertJson([
            'status' => false,
            'message' => 'Unauthorized'
        ]);
    }

    public function testDeleteGamesSuccess()
    {
        Passport::actingAs($this->regularUser);
    
        Game::factory()->count(3)->create(['user_id' => $this->regularUser->id]);
    
        $response = $this->deleteJson("/api/players/{$this->regularUser->id}/games");
    
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Games deleted']);
        $this->assertDatabaseCount('games', 0);
    }

    public function testDeleteGamesUnauthorized()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Passport::actingAs($user1);

        $response = $this->deleteJson("/api/players/{$user2->id}/games");

        $response->assertStatus(403);
        $response->assertJson([
            'status' => false,         
            'message' => 'Unauthorized' 
        ]);
    }

    public function testGetGamesSuccess()
    {
        Passport::actingAs($this->regularUser);
    
        Game::factory()->count(3)->create(['user_id' => $this->regularUser->id]);
    
        $response = $this->getJson("/api/players/{$this->regularUser->id}/games");
    
        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    public function testGetGamesUnauthorized()
    {
        // Setup: Crear dos usuarios
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
    
        // Autenticar como el primer usuario
        Passport::actingAs($user1);
    
        // Action: Intentar obtener los juegos del segundo usuario
        $response = $this->getJson("/api/players/{$user2->id}/games");
    
        // Assert: Verificar la respuesta
        $response->assertStatus(403);
        $response->assertJson([
            'status' => false,
            'message' => 'Unauthorized'
        ]);
    }
}

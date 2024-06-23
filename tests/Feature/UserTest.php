<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Game;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Passport;
use Spatie\Permission\Models\Role;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $regularUser;

    public function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin']);
        Role::create(['name' => 'user']);

        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole('admin');

        $this->regularUser = User::factory()->create();
        $this->regularUser->assignRole('user');
    }

    public function testGetAllPlayersSuccess()
    {
        Passport::actingAs($this->adminUser);

        $users = User::factory()->count(3)->create();

        $response = $this->getJson('/api/players');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'users' => [
                '*' => [
                    'id',
                    'name',
                    'email',
                    'email_verified_at',
                    'role',
                    'created_at',
                    'updated_at',
                ]
            ]
        ]);
    }
    
    public function testGetAllPlayersEmpty()
    {
        Passport::actingAs($this->adminUser);
    
        $response = $this->getJson('/api/players');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'users'
        ]);
    }

    public function testGetRankingSuccess()
    {
        // Setup: Create users and games
        $users = User::factory()->count(3)->create();
        foreach ($users as $user) {
            Game::factory()->count(10)->create([
                'user_id' => $user->id,
                'win' => true,
            ]);
        }

        // Authenticate as an admin user
        Passport::actingAs($this->adminUser);

        // Action: Call the endpoint with an Authorization header
        $response = $this->getJson('/api/players/ranking');

        // Assert: Check the response
        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'user',
                'success_percentage'
            ]
        ]);
    }

    public function testGetRankingNoGames()
    {
        // Setup: Create users with no games
        User::factory()->count(3)->create();

        Passport::actingAs($this->adminUser);

        // Action: Call the endpoint
        $response = $this->getJson('/api/players/ranking');

        // Assert: Check the response
        $response->assertStatus(200);
        $response->assertJsonFragment(['success_percentage' => 0]);
    }

    public function testGetWinnerSuccess()
    {
        // Setup: Create users and games
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Game::factory()->count(5)->create([
            'user_id' => $user1->id,
            'win' => true,
        ]);

        Game::factory()->count(2)->create([
            'user_id' => $user2->id,
            'win' => true,
        ]);

        Passport::actingAs($this->adminUser);

        // Action: Call the endpoint with an Authorization header
        $response = $this->getJson('/api/players/ranking/winner');

        // Assert: Check the response
        $response->assertStatus(200);
        $response->assertJson([
            'winner' => [
                'name' => $user1->name,
                'wins' => 5,
            ],
        ]);
    }

    public function testGetWinnerNoWinner()
    {
        Passport::actingAs($this->adminUser);
    
        // Action: Call the endpoint with an Authorization header
        $response = $this->getJson('/api/players/ranking/winner');
    
        // Assert: Check the response
        $response->assertStatus(404);
        $response->assertJson(['error' => 'No winner found']);
    }

    public function testGetLoserSuccess()
    {
        // Setup: Create users and games
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
    
        Game::factory()->count(10)->create([
            'user_id' => $user1->id,
            'win' => true,
        ]);
    
        Game::factory()->count(10)->create([
            'user_id' => $user2->id,
            'win' => false,
        ]);
    
        Passport::actingAs($this->adminUser);
    
        // Action: Call the endpoint with an Authorization header
        $response = $this->getJson('/api/players/ranking/loser');
    
        $response->assertStatus(200);
    }

    public function testGetLoserNoPlayersFound()
    {
        Passport::actingAs($this->adminUser);

        User::query()->delete();

        $response = $this->get('/api/players/ranking/loser');

        $response->assertStatus(404)
                 ->assertJson([
                     'error' => 'No players found'
                 ]);
    }

}

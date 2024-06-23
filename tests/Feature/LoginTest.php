<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Laravel\Passport\Passport;

class LoginTest extends TestCase
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

    // public function testLoginSuccessfully()
    // {
    //     $user = User::factory()->create([
    //         'email' => 'test@example.com',
    //         'password' => bcrypt('password'),
    //     ]);

    //     $response = $this->postJson('/api/login', [
    //         'email' => 'test@example.com',
    //         'password' => 'password',
    //     ]);

    //     $response->assertStatus(200);
    // }

    public function testValidationFailsWith_MissingFields()
    {
        $response = $this->postJson('/api/login', []);

        $response->assertStatus(422);
    }

    public function testLoginWithInvalidCredentials()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => bcrypt('password123')
        ]);
    
        $data = [
            'email' => 'john@example.com',
            'password' => 'wrongpassword'
        ];
    
        $response = $this->postJson('/api/login', $data);
    
        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthorized']);
    }

    public function testLoginFailsWithNonExistentEmail()
    {
        $data = [
            'email' => 'nonexistent@example.com',
            'password' => 'password123'
        ];
    
        $response = $this->postJson('/api/login', $data);
    
        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthorized']);
    }
}

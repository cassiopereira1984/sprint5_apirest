<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Spatie\Permission\Models\Role;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'user']);
    }

    public function testRegisterUserSuccessfully()
    {
        $response = $this->postJson('/api/players', [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => 'password123',
            'role' => 'user',
        ]);
    
        $response->assertStatus(201)
                 ->assertJson([
                     'message' => 'Successfully created user!',
                     'roles' => ['user'],
                 ]);
    
        $this->assertDatabaseHas('users', [
            'email' => 'johndoe@example.com',
        ]);
        
        $user = User::where('email', 'johndoe@example.com')->first();
        $this->assertTrue($user->hasRole('user'));
    }

    public function testRegisterAdminSuccessfully()
    {
        $response = $this->postJson('/api/players', [
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'adminpass123',
            'role' => 'admin',
        ]);
    
        $response->assertStatus(201)
                 ->assertJson([
                     'message' => 'Successfully created user!',
                     'roles' => ['admin'],
                 ]);
    
        $this->assertDatabaseHas('users', [
            'email' => 'admin@example.com',
        ]);
        
        $user = User::where('email', 'admin@example.com')->first();
        $this->assertTrue($user->hasRole('admin'));
    }

    public function testRegisterValidationErrors()
    {
        $response = $this->postJson('/api/players', [
            'email' => 'invalid-email',
            'password' => 'short', 
        ]);
    
        $response->assertStatus(422)
                 ->assertJsonStructure([
                     'email',
                     'password',
                     'role',
                 ]);
    }
}
<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;


class LogoutTest extends TestCase
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
    
    public function testLogoutSuccessfully()
    { 
        Passport::actingAs($this->adminUser);

        $response = $this->postJson("/api/logout");
    
        $response->assertStatus(200);
    }

    public function testLogoutUnauthenticated()
    {
        $response = $this->postJson("/api/logout");

        $response->assertStatus(401);

        $response->assertJsonStructure(['message']);
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Passport\Passport;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    public function testUpdateUserName()
    {
        $user = User::factory()->create([
            'name' => 'name1',
        ]);

        Passport::actingAs($user);

        $newName = 'name2';

        $response = $this->putJson("/api/players/{$user->id}", [
            'name' => $newName,
        ]);

        $response->assertStatus(200);

        $user->refresh();

        $this->assertEquals($newName, $user->name);
    }

    public function testUpdateReturnsUnauthorizedIfNotOwner()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Passport::actingAs($user1);

        $response = $this->putJson("/api/players/{$user2->id}", [
            'name' => 'Updated Name',
        ]);

        $response->assertStatus(403);

        $response->assertJson([
            'error' => 'Unauthorized',
        ]);
    }

    public function testUpdateNameWhenEmpty()
    {
        $user = User::factory()->create([
            'name' => 'name1',
        ]);

        Passport::actingAs($user);

        $response = $this->putJson("/api/players/{$user->id}", [
            'name' => '',
        ]);

        $response->assertStatus(200);

        $user->refresh();

        $this->assertEquals('Anonymous', $user->name);
    }
}

<?php

namespace Tests\Feature;

use App\Domain\Identity\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_landlord_can_store_property(): void
    {
        $user = User::factory()->create(['role' => 'landlord']);

        $response = $this->actingAs($user)->postJson('/properties', [
            'name' => 'Test Property',
            'address' => '123 Test St',
            'price' => 1500.50,
            'type' => 'Apartment',
            'description' => 'A test property description.',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('name', 'Test Property')
            ->assertJsonPath('price', 1500.50);

        $this->assertDatabaseHas('properties', [
            'name' => 'Test Property',
            'user_id' => $user->id,
        ]);
    }

    public function test_guest_cannot_store_property(): void
    {
        $response = $this->postJson('/properties', [
            'name' => 'Test Property',
        ]);

        $response->assertStatus(401);
    }

    public function test_store_property_validation(): void
    {
        $user = User::factory()->create(['role' => 'landlord']);

        $response = $this->actingAs($user)->postJson('/properties', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'address', 'price', 'type']);
    }
}

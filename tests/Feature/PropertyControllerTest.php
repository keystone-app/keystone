<?php

namespace Tests\Feature;

use App\Domain\Identity\Models\User;
use App\Domain\Legal\Models\Document;
use App\Domain\Property\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    public function test_landlord_can_store_property_with_media(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => 'landlord']);

        $response = $this->actingAs($user)->postJson('/properties', [
            'name' => 'Media Property',
            'address' => '456 Media Ave',
            'price' => 2000.00,
            'type' => 'House',
            'images' => [
                UploadedFile::fake()->image('house1.jpg'),
                UploadedFile::fake()->image('house2.png'),
            ],
            'videos' => [
                UploadedFile::fake()->create('tour.mp4', 5000, 'video/mp4'),
            ],
        ]);

        $response->assertStatus(201);

        $propertyId = $response->json('id');

        $this->assertDatabaseHas('documents', [
            'documentable_type' => Property::class,
            'documentable_id' => $propertyId,
            'type' => 'property_image',
            'name' => 'house1.jpg',
        ]);

        $this->assertDatabaseHas('documents', [
            'documentable_type' => Property::class,
            'documentable_id' => $propertyId,
            'type' => 'property_video',
            'name' => 'tour.mp4',
        ]);

        $imageDoc = Document::where('documentable_id', $propertyId)
            ->where('type', 'property_image')
            ->first();
        $this->assertNotNull($imageDoc);
        Storage::disk('public')->assertExists($imageDoc->path);

        $videoDoc = Document::where('documentable_id', $propertyId)
            ->where('type', 'property_video')
            ->first();
        $this->assertNotNull($videoDoc);
        Storage::disk('public')->assertExists($videoDoc->path);
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

    public function test_index_filters_properties(): void
    {
        \App\Domain\Property\Models\Property::factory()->create(['price' => 1000, 'type' => 'Apartment', 'status' => 'available']);
        \App\Domain\Property\Models\Property::factory()->create(['price' => 2000, 'type' => 'House', 'status' => 'rented']);

        $response = $this->getJson('/properties?min_price=1500&max_price=2500&type=House&status=rented');

        $response->assertStatus(200)
            ->assertJsonCount(1);
        
        $this->assertEquals(2000, $response->json('0.price'));
    }

    public function test_store_requires_authentication(): void
    {
        // Explicitly NOT actingAs
        $response = $this->postJson('/properties', [
            'name' => 'Test',
            'address' => 'Test',
            'price' => 1000,
            'type' => 'Apartment',
        ]);
        
        $response->assertStatus(401);
    }
}

<?php

namespace Tests\Feature;

use App\Domain\Identity\Models\User;
use App\Domain\Legal\Models\Lease;
use App\Domain\Legal\States\Draft;
use App\Domain\Property\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LeaseControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_their_leases(): void
    {
        $tenant = User::factory()->tenant()->create();
        $landlord = User::factory()->landlord()->create();
        $property = Property::factory()->create(['user_id' => $landlord->id]);
        
        $lease = Lease::factory()->create([
            'tenant_id' => $tenant->id,
            'landlord_id' => $landlord->id,
            'property_id' => $property->id,
        ]);

        $otherLease = Lease::factory()->create();

        $response = $this->actingAs($tenant)->getJson('/leases');

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonPath('0.id', $lease->id);
    }

    public function test_index_requires_authentication(): void
    {
        $response = $this->getJson('/leases');
        $response->assertStatus(401);
    }

    public function test_unauthorized_user_cannot_upload_document_to_lease(): void
    {
        Storage::fake('public');
        $lease = Lease::factory()->create();
        $stranger = User::factory()->create();

        $response = $this->actingAs($stranger)->postJson("/leases/{$lease->id}/upload", [
            'type' => 'some_doc',
            'file' => UploadedFile::fake()->create('test.pdf'),
        ]);

        $response->assertStatus(403);
    }

    public function test_upload_document_validation_fails(): void
    {
        $lease = Lease::factory()->create();
        $this->actingAs($lease->tenant);

        $response = $this->postJson("/leases/{$lease->id}/upload", []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['type', 'file']);
    }

    public function test_unauthorized_user_cannot_sign_lease(): void
    {
        $lease = Lease::factory()->create(['status' => Draft::class]);
        $stranger = User::factory()->create();

        $response = $this->actingAs($stranger)->postJson("/leases/{$lease->id}/sign");

        $response->assertStatus(403);
    }
}

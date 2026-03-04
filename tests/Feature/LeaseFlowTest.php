<?php

namespace Tests\Feature;

use App\Domain\Identity\Models\User;
use App\Domain\Legal\Models\Lease;
use App\Domain\Legal\States\Active;
use App\Domain\Legal\States\Draft;
use App\Domain\Legal\States\WaitingLandlordSignature;
use App\Domain\Legal\States\WaitingTenantSignature;
use App\Domain\Negotiation\Models\Offer;
use App\Domain\Property\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LeaseFlowTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function accepting_an_offer_creates_a_lease_draft()
    {
        $landlord = User::factory()->landlord()->create();
        $property = Property::factory()->create(['user_id' => $landlord->id]);
        $offer = Offer::factory()->create([
            'property_id' => $property->id,
            'status' => 'pending',
        ]);

        $this->actingAs($landlord)->patchJson("/offers/{$offer->id}", [
            'status' => 'accepted',
        ]);

        $this->assertDatabaseHas('leases', [
            'property_id' => $property->id,
            'landlord_id' => $landlord->id,
            'tenant_id' => $offer->user_id,
            'rent_amount' => $offer->amount,
            'status' => 'draft',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function lease_transitions_to_waiting_signature_after_both_parties_upload_docs()
    {
        Storage::fake('public');
        $landlord = User::factory()->landlord()->create();
        $tenant = User::factory()->tenant()->create();
        $lease = Lease::factory()->create([
            'landlord_id' => $landlord->id,
            'tenant_id' => $tenant->id,
            'status' => Draft::class,
        ]);

        // Landlord uploads
        $this->actingAs($landlord)->postJson("/leases/{$lease->id}/upload", [
            'type' => 'landlord_id',
            'file' => UploadedFile::fake()->image('landlord.jpg'),
        ]);

        $this->assertInstanceOf(Draft::class, $lease->fresh()->status);

        // Tenant uploads
        $this->actingAs($tenant)->postJson("/leases/{$lease->id}/upload", [
            'type' => 'tenant_id',
            'file' => UploadedFile::fake()->image('tenant.jpg'),
        ]);

        $this->assertInstanceOf(WaitingLandlordSignature::class, $lease->fresh()->status);

        // Should have the generated lease agreement doc
        $this->assertDatabaseHas('documents', [
            'lease_id' => $lease->id,
            'type' => 'lease_agreement',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function lease_transitions_through_signature_flow_to_active()
    {
        $landlord = User::factory()->landlord()->create();
        $tenant = User::factory()->tenant()->create();
        $lease = Lease::factory()->create([
            'landlord_id' => $landlord->id,
            'tenant_id' => $tenant->id,
            'status' => WaitingLandlordSignature::class,
        ]);

        // Landlord signs
        $response = $this->actingAs($landlord)->postJson("/leases/{$lease->id}/sign");
        $response->assertStatus(200);
        $this->assertInstanceOf(WaitingTenantSignature::class, $lease->fresh()->status);

        // Tenant signs
        $response = $this->actingAs($tenant)->postJson("/leases/{$lease->id}/sign");
        $response->assertStatus(200);
        $this->assertInstanceOf(Active::class, $lease->fresh()->status);
    }
}

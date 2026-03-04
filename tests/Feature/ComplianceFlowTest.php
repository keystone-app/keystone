<?php

namespace Tests\Feature;

use App\Domain\Identity\Models\User;
use App\Domain\Negotiation\Models\Offer;
use App\Domain\Property\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ComplianceFlowTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function an_accepted_offer_transitions_to_awaiting_documents()
    {
        $landlord = User::factory()->landlord()->create();
        $property = Property::factory()->create(['user_id' => $landlord->id]);
        $offer = Offer::factory()->create([
            'property_id' => $property->id,
            'status' => 'pending',
            'compliance_status' => 'none',
        ]);

        $response = $this->actingAs($landlord)->patchJson("/offers/{$offer->id}", [
            'status' => 'accepted',
        ]);

        $response->assertStatus(200);
        $this->assertEquals('accepted', $offer->fresh()->status);
        $this->assertEquals('awaiting_documents', $offer->fresh()->compliance_status);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function a_tenant_can_upload_compliance_documents()
    {
        Storage::fake('public');
        $tenant = User::factory()->tenant()->create();
        $offer = Offer::factory()->create([
            'user_id' => $tenant->id,
            'compliance_status' => 'awaiting_documents',
        ]);

        $file = UploadedFile::fake()->image('income.jpg');

        $response = $this->actingAs($tenant)->postJson('/compliance-upload', [
            'offer_id' => $offer->id,
            'type' => 'income_proof',
            'file' => $file,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('documents', [
            'user_id' => $tenant->id,
            'type' => 'income_proof',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function offer_transitions_to_pending_verification_after_both_docs_are_uploaded()
    {
        Storage::fake('public');
        $tenant = User::factory()->tenant()->create();
        $offer = Offer::factory()->create([
            'user_id' => $tenant->id,
            'compliance_status' => 'awaiting_documents',
        ]);

        // Upload first doc
        $this->actingAs($tenant)->postJson('/compliance-upload', [
            'offer_id' => $offer->id,
            'type' => 'income_proof',
            'file' => UploadedFile::fake()->image('income.jpg'),
        ]);

        $this->assertEquals('awaiting_documents', $offer->fresh()->compliance_status);

        // Upload second doc
        $this->actingAs($tenant)->postJson('/compliance-upload', [
            'offer_id' => $offer->id,
            'type' => 'residency_proof',
            'file' => UploadedFile::fake()->image('residency.jpg'),
        ]);

        $this->assertEquals('pending_verification', $offer->fresh()->compliance_status);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function a_tenant_can_trigger_open_finance_verification()
    {
        $tenant = User::factory()->tenant()->create();
        $offer = Offer::factory()->create([
            'user_id' => $tenant->id,
            'compliance_status' => 'pending_verification',
        ]);

        $response = $this->actingAs($tenant)->postJson("/offers/{$offer->id}/verify");

        $response->assertStatus(200);
        $this->assertEquals('verified', $offer->fresh()->compliance_status);
        $response->assertJsonPath('verification.provider', 'Brazil Open Finance');
    }
}

<?php

namespace Tests\Feature;

use App\Domain\Identity\Models\User;
use App\Domain\Legal\Actions\UploadComplianceDocumentAction;
use App\Domain\Legal\Models\Document;
use App\Domain\Negotiation\Models\Offer;
use App\Domain\Negotiation\States\AwaitingDocuments;
use App\Domain\Negotiation\States\PendingVerification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ComplianceFlowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_transitions_offer_to_pending_verification_when_all_documents_are_uploaded()
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => 'tenant']);
        $this->actingAs($user);

        // Create an offer in AwaitingDocuments state
        $offer = Offer::factory()->create([
            'user_id' => $user->id,
            'status' => AwaitingDocuments::class,
        ]);

        $action = app(UploadComplianceDocumentAction::class);

        // 1. Upload first document (income_proof)
        $incomeFile = UploadedFile::fake()->create('income.pdf', 500);
        $action->execute($offer, 'income_proof', $incomeFile);

        $offer->refresh();
        $this->assertInstanceOf(AwaitingDocuments::class, $offer->status);
        $this->assertDatabaseHas('documents', [
            'user_id' => $user->id,
            'documentable_type' => Offer::class,
            'documentable_id' => $offer->id,
            'type' => 'income_proof',
            'name' => 'income.pdf',
        ]);

        // 2. Upload second document (residency_proof)
        $residencyFile = UploadedFile::fake()->create('residency.pdf', 500);
        $action->execute($offer, 'residency_proof', $residencyFile);

        // Verify transition
        $offer->refresh();
        $this->assertInstanceOf(PendingVerification::class, $offer->status);
        $this->assertDatabaseHas('documents', [
            'user_id' => $user->id,
            'documentable_type' => Offer::class,
            'documentable_id' => $offer->id,
            'type' => 'residency_proof',
            'name' => 'residency.pdf',
        ]);

        // Verify relationship visibility
        $this->assertCount(2, $offer->complianceDocuments);
    }
}

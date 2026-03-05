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

use App\Domain\Negotiation\Actions\VerifyIncomeAction;
use App\Domain\Negotiation\States\Verified;

use App\Domain\Legal\Models\Lease;
use App\Domain\Legal\States\Draft;

class ComplianceFlowTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_automatically_creates_a_lease_draft_when_offer_is_verified(): void
    {
        $user = User::factory()->create(['role' => 'tenant']);
        $this->actingAs($user);

        $offer = Offer::factory()->create([
            'user_id' => $user->id,
            'status' => PendingVerification::class,
            'amount' => 3000,
        ]);

        $action = app(VerifyIncomeAction::class);
        $action->execute($offer);

        $this->assertDatabaseHas('leases', [
            'tenant_id' => $user->id,
            'property_id' => $offer->property_id,
            'rent_amount' => 3000,
            'status' => 'draft',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function a_tenant_can_verify_their_income_when_offer_is_pending_verification(): void
    {
        $user = User::factory()->create(['role' => 'tenant']);
        $this->actingAs($user);

        $offer = Offer::factory()->create([
            'user_id' => $user->id,
            'status' => PendingVerification::class,
            'amount' => 3000,
        ]);

        $action = app(VerifyIncomeAction::class);
        $result = $action->execute($offer);

        $offer->refresh();
        $this->assertInstanceOf(Verified::class, $offer->status);
        $this->assertEquals('success', $result['verification']['status']);
        $this->assertEquals(3000 * 3.5, $result['verification']['monthly_income_verified']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_fails_to_verify_income_if_unauthorized(): void
    {
        $user = User::factory()->create(['role' => 'tenant']);
        $otherUser = User::factory()->create(['role' => 'tenant']);
        $this->actingAs($otherUser);

        $offer = Offer::factory()->create([
            'user_id' => $user->id,
            'status' => PendingVerification::class,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unauthorized');

        $action = app(VerifyIncomeAction::class);
        $action->execute($offer);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_fails_to_verify_income_if_not_in_pending_verification_status(): void
    {
        $user = User::factory()->create(['role' => 'tenant']);
        $this->actingAs($user);

        $offer = Offer::factory()->create([
            'user_id' => $user->id,
            'status' => AwaitingDocuments::class,
        ]);

        $this->expectException(\Spatie\ModelStates\Exceptions\TransitionNotFound::class);

        $action = app(VerifyIncomeAction::class);
        $action->execute($offer);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_transitions_offer_to_pending_verification_when_all_documents_are_uploaded(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_does_not_transition_when_only_one_document_type_is_uploaded(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => 'tenant']);
        $this->actingAs($user);

        $offer = Offer::factory()->create([
            'user_id' => $user->id,
            'status' => AwaitingDocuments::class,
        ]);

        $action = app(UploadComplianceDocumentAction::class);

        // Upload two of the same type (e.g., both income_proof)
        $file1 = UploadedFile::fake()->create('income1.pdf', 500);
        $action->execute($offer, 'income_proof', $file1);
        
        $file2 = UploadedFile::fake()->create('income2.pdf', 500);
        $action->execute($offer, 'income_proof', $file2);

        $offer->refresh();
        $this->assertInstanceOf(AwaitingDocuments::class, $offer->status);
        $this->assertCount(2, $offer->complianceDocuments);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_does_not_transition_when_documents_belong_to_another_offer(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => 'tenant']);
        $this->actingAs($user);

        $offer1 = Offer::factory()->create([
            'user_id' => $user->id,
            'status' => AwaitingDocuments::class,
        ]);
        
        $offer2 = Offer::factory()->create([
            'user_id' => $user->id,
            'status' => AwaitingDocuments::class,
        ]);

        $action = app(UploadComplianceDocumentAction::class);

        // Upload income proof to offer 1
        $action->execute($offer1, 'income_proof', UploadedFile::fake()->create('income1.pdf', 500));
        
        // Upload residency proof to offer 2
        $action->execute($offer2, 'residency_proof', UploadedFile::fake()->create('residency2.pdf', 500));

        $offer1->refresh();
        $offer2->refresh();
        
        $this->assertInstanceOf(AwaitingDocuments::class, $offer1->status);
        $this->assertInstanceOf(AwaitingDocuments::class, $offer2->status);
    }
}

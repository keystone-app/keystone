<?php

namespace Tests\Unit\Domain;

use App\Domain\Identity\Models\User;
use App\Domain\Legal\Models\Document;
use App\Domain\Legal\Models\Lease;
use App\Domain\Negotiation\Models\Offer;
use App\Domain\Property\Models\Property;
use App\Domain\Scheduling\Models\Visit;
use App\Domain\Maintenance\Models\MaintenanceRequest;
use App\Domain\Negotiation\States\Accepted;
use App\Domain\Negotiation\States\AwaitingDocuments;
use App\Domain\Negotiation\States\Pending;
use App\Domain\Negotiation\States\PendingVerification;
use App\Domain\Negotiation\States\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelsTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_model_is_complete(): void
    {
        $landlord = User::factory()->landlord()->create();
        $tenant = User::factory()->tenant()->create();
        $guest = User::factory()->create(['role' => 'guest']);

        $this->assertTrue($landlord->isLandlord());
        $this->assertFalse($landlord->isTenant());
        $this->assertTrue($tenant->isTenant());
        $this->assertFalse($tenant->isLandlord());
        $this->assertFalse($guest->isLandlord());
        $this->assertFalse($guest->isTenant());

        Property::factory()->create(['user_id' => $landlord->id]);
        $this->assertInstanceOf(Property::class, $landlord->properties->first());
        $this->assertCount(1, $landlord->properties);

        Lease::factory()->create(['landlord_id' => $landlord->id, 'tenant_id' => $tenant->id]);
        $this->assertInstanceOf(Lease::class, $landlord->landlordLeases->first());
        $this->assertInstanceOf(Lease::class, $tenant->tenantLeases->first());
        $this->assertCount(1, $landlord->landlordLeases);
        $this->assertCount(1, $tenant->tenantLeases);

        Visit::factory()->create(['user_id' => $tenant->id]);
        $this->assertInstanceOf(Visit::class, $tenant->visits->first());
        $this->assertCount(1, $tenant->visits);

        Document::factory()->create(['user_id' => $tenant->id]);
        $this->assertInstanceOf(Document::class, $tenant->documents->first());
        $this->assertCount(1, $tenant->documents);

        Offer::factory()->create(['user_id' => $tenant->id]);
        $this->assertInstanceOf(Offer::class, $tenant->offers->first());
        $this->assertCount(1, $tenant->offers);

        $this->assertNull($tenant->getIdentityDocument());
        $idDoc = Document::factory()->create(['user_id' => $tenant->id]);
        $tenant->update(['identity_document_id' => $idDoc->id]);
        $this->assertTrue($tenant->fresh()->hasIdentityDocument());
        $this->assertInstanceOf(Document::class, $tenant->fresh()->getIdentityDocument());
        $this->assertInstanceOf(Document::class, $tenant->fresh()->identityDocument);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function document_model_is_complete(): void
    {
        $user = User::factory()->create();
        $doc = Document::factory()->create(['user_id' => $user->id]);
        $this->assertInstanceOf(User::class, $doc->user);

        $lease = Lease::factory()->create();
        $doc->update(['lease_id' => $lease->id]);
        $this->assertInstanceOf(Lease::class, $doc->fresh()->lease);

        $offer = Offer::factory()->create();
        $doc->update(['documentable_id' => $offer->id, 'documentable_type' => Offer::class]);
        $this->assertInstanceOf(Offer::class, $doc->fresh()->documentable);
        
        $property = Property::factory()->create();
        $doc->update(['documentable_id' => $property->id, 'documentable_type' => Property::class]);
        $this->assertInstanceOf(Property::class, $doc->fresh()->documentable);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function lease_model_is_complete(): void
    {
        $lease = Lease::factory()->create();
        $this->assertInstanceOf(Property::class, $lease->property);
        $this->assertInstanceOf(User::class, $lease->landlord);
        $this->assertInstanceOf(User::class, $lease->tenant);
        
        Document::factory()->create(['lease_id' => $lease->id]);
        $this->assertCount(1, $lease->documents);
        $this->assertInstanceOf(Document::class, $lease->documents->first());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function property_model_is_complete(): void
    {
        $property = Property::factory()->create();
        $this->assertInstanceOf(User::class, $property->user);
        
        Document::factory()->create(['documentable_id' => $property->id, 'documentable_type' => Property::class]);
        $this->assertCount(1, $property->media);
        $this->assertInstanceOf(Document::class, $property->media->first());

        Lease::factory()->create(['property_id' => $property->id]);
        $this->assertCount(1, $property->leases);
        $this->assertInstanceOf(Lease::class, $property->leases->first());

        Offer::factory()->create(['property_id' => $property->id]);
        $this->assertCount(1, $property->offers);
        $this->assertInstanceOf(Offer::class, $property->offers->first());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function visit_model_is_complete(): void
    {
        $visit = Visit::factory()->create();
        $this->assertInstanceOf(User::class, $visit->user);
        $this->assertInstanceOf(Property::class, $visit->property);
        $this->assertInstanceOf(Document::class, $visit->document);

        Offer::factory()->create(['visit_id' => $visit->id]);
        $this->assertInstanceOf(Offer::class, $visit->fresh()->offer);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function offer_model_is_complete(): void
    {
        $offer = Offer::factory()->create(['status' => Pending::class]);
        $this->assertInstanceOf(User::class, $offer->user);
        $this->assertInstanceOf(Property::class, $offer->property);
        $this->assertInstanceOf(Visit::class, $offer->visit);

        Document::factory()->create(['user_id' => $offer->user_id, 'type' => 'income_proof']);
        $this->assertCount(1, $offer->complianceDocuments);

        $this->assertEquals('none', $offer->compliance_status_label);
        $offer->status->transitionTo(Accepted::class);
        $offer->status->transitionTo(AwaitingDocuments::class);
        $this->assertEquals('awaiting_documents', $offer->compliance_status_label);
        $offer->status->transitionTo(PendingVerification::class);
        $this->assertEquals('pending_verification', $offer->compliance_status_label);
        $offer->status->transitionTo(Verified::class);
        $this->assertEquals('verified', $offer->compliance_status_label);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function maintenance_request_model_is_complete(): void
    {
        $request = MaintenanceRequest::factory()->create();
        $this->assertInstanceOf(User::class, $request->user);
        $this->assertInstanceOf(Lease::class, $request->lease);
    }
}

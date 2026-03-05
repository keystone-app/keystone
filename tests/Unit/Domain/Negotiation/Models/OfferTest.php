<?php

namespace Tests\Unit\Domain\Negotiation\Models;

use App\Domain\Identity\Models\User;
use App\Domain\Legal\Models\Document;
use App\Domain\Negotiation\Models\Offer;
use App\Domain\Negotiation\States\Accepted;
use App\Domain\Negotiation\States\AwaitingDocuments;
use App\Domain\Negotiation\States\Pending;
use App\Domain\Negotiation\States\PendingVerification;
use App\Domain\Negotiation\States\Verified;
use App\Domain\Property\Models\Property;
use App\Domain\Scheduling\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfferTest extends TestCase
{
    use RefreshDatabase;

    public function test_offer_belongs_to_a_user(): void
    {
        $user = User::factory()->create();
        $offer = Offer::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $offer->user);
        $this->assertEquals($user->id, $offer->user->id);
    }

    public function test_offer_belongs_to_a_property(): void
    {
        $property = Property::factory()->create();
        $offer = Offer::factory()->create(['property_id' => $property->id]);

        $this->assertInstanceOf(Property::class, $offer->property);
        $this->assertEquals($property->id, $offer->property->id);
    }

    public function test_offer_belongs_to_a_visit(): void
    {
        $visit = Visit::factory()->create();
        $offer = Offer::factory()->create(['visit_id' => $visit->id]);

        $this->assertInstanceOf(Visit::class, $offer->visit);
        $this->assertEquals($visit->id, $offer->visit->id);
    }

    public function test_offer_has_compliance_documents(): void
    {
        $user = User::factory()->create();
        $offer = Offer::factory()->create(['user_id' => $user->id]);
        
        Document::factory()->create([
            'user_id' => $user->id,
            'type' => 'income_proof',
        ]);

        Document::factory()->create([
            'user_id' => $user->id,
            'type' => 'residency_proof',
        ]);

        $this->assertCount(2, $offer->complianceDocuments);
    }

    public function test_offer_can_have_visit_relationship(): void
    {
        $visit = Visit::factory()->create();
        $offer = Offer::factory()->create(['visit_id' => $visit->id]);

        $this->assertInstanceOf(Visit::class, $offer->visit);
        $this->assertEquals($visit->id, $offer->visit->id);
    }

    public function test_offer_compliance_status_label_attribute(): void
    {
        $offer = Offer::factory()->create(['status' => Pending::class]);
        $this->assertEquals('none', $offer->compliance_status_label);

        $offer->status->transitionTo(Accepted::class);
        $offer->status->transitionTo(AwaitingDocuments::class);
        $this->assertEquals('awaiting_documents', $offer->compliance_status_label);

        $offer->status->transitionTo(PendingVerification::class);
        $this->assertEquals('pending_verification', $offer->compliance_status_label);

        $offer->status->transitionTo(Verified::class);
        $this->assertEquals('verified', $offer->compliance_status_label);
    }
}

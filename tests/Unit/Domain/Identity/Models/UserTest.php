<?php

namespace Tests\Unit\Domain\Identity\Models;

use App\Domain\Identity\Models\User;
use App\Domain\Legal\Models\Document;
use App\Domain\Legal\Models\Lease;
use App\Domain\Negotiation\Models\Offer;
use App\Domain\Property\Models\Property;
use App\Domain\Scheduling\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_model_functionality(): void
    {
        $landlord = User::factory()->landlord()->create();
        $tenant = User::factory()->tenant()->create();

        // Role checks
        $this->assertTrue($landlord->isLandlord());
        $this->assertFalse($landlord->isTenant());
        $this->assertTrue($tenant->isTenant());
        $this->assertFalse($tenant->isLandlord());

        $guest = User::factory()->create(['role' => 'guest']);
        $this->assertFalse($guest->isLandlord());
        $this->assertFalse($guest->isTenant());

        // Relationships
        Property::factory()->create(['user_id' => $landlord->id]);
        $this->assertCount(1, $landlord->properties);

        Lease::factory()->create(['landlord_id' => $landlord->id, 'tenant_id' => $tenant->id]);
        $this->assertCount(1, $landlord->landlordLeases);
        $this->assertCount(1, $tenant->tenantLeases);

        Visit::factory()->create(['user_id' => $tenant->id]);
        $this->assertCount(1, $tenant->visits);

        $doc = Document::factory()->create(['user_id' => $tenant->id]);
        $this->assertCount(1, $tenant->documents);
        $this->assertInstanceOf(Document::class, $tenant->documents->first());

        Offer::factory()->create(['user_id' => $tenant->id]);
        $this->assertCount(1, $tenant->offers);
        $this->assertInstanceOf(Offer::class, $tenant->offers->first());

        // Identity Document methods
        $this->assertFalse($tenant->hasIdentityDocument());
        $this->assertNull($tenant->getIdentityDocument());

        $idDoc = Document::factory()->create(['user_id' => $tenant->id]);
        $tenant->update(['identity_document_id' => $idDoc->id]);

        $this->assertTrue($tenant->fresh()->hasIdentityDocument());
        $this->assertInstanceOf(Document::class, $tenant->fresh()->getIdentityDocument());
        $this->assertEquals($idDoc->id, $tenant->fresh()->getIdentityDocument()->id);
    }
}

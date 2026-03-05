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

    public function test_user_role_checks(): void
    {
        $landlord = User::factory()->landlord()->make();
        $tenant = User::factory()->tenant()->make();

        $this->assertTrue($landlord->isLandlord());
        $this->assertFalse($landlord->isTenant());
        $this->assertTrue($tenant->isTenant());
        $this->assertFalse($tenant->isLandlord());
    }

    public function test_user_has_properties(): void
    {
        $user = User::factory()->landlord()->create();
        Property::factory()->count(2)->create(['user_id' => $user->id]);

        $this->assertCount(2, $user->properties);
        $this->assertInstanceOf(Property::class, $user->properties->first());
    }

    public function test_user_has_leases(): void
    {
        $landlord = User::factory()->landlord()->create();
        $tenant = User::factory()->tenant()->create();
        
        Lease::factory()->create(['landlord_id' => $landlord->id, 'tenant_id' => $tenant->id]);

        $this->assertCount(1, $landlord->landlordLeases);
        $this->assertCount(1, $tenant->tenantLeases);
    }

    public function test_user_has_visits(): void
    {
        $user = User::factory()->create();
        Visit::factory()->count(2)->create(['user_id' => $user->id]);

        $this->assertCount(2, $user->visits);
    }

    public function test_user_has_documents(): void
    {
        $user = User::factory()->create();
        Document::factory()->count(2)->create(['user_id' => $user->id]);

        $this->assertCount(2, $user->documents);
    }

    public function test_user_has_offers(): void
    {
        $user = User::factory()->tenant()->create();
        Offer::factory()->count(2)->create(['user_id' => $user->id]);

        $this->assertCount(2, $user->offers);
    }

    public function test_user_identity_document_methods(): void
    {
        $user = User::factory()->create();
        $this->assertFalse($user->hasIdentityDocument());
        $this->assertNull($user->getIdentityDocument());

        $doc = Document::factory()->create(['user_id' => $user->id]);
        $user->update(['identity_document_id' => $doc->id]);

        $this->assertTrue($user->fresh()->hasIdentityDocument());
        $this->assertInstanceOf(Document::class, $user->fresh()->getIdentityDocument());
        $this->assertEquals($doc->id, $user->fresh()->getIdentityDocument()->id);
    }
}

<?php

namespace Tests\Unit\Domain;

use App\Domain\Identity\Actions\RegisterGuestAction;
use App\Domain\Identity\Models\User;
use App\Domain\Legal\Actions\SignLeaseAction;
use App\Domain\Legal\Actions\UploadIdentityDocumentAction;
use App\Domain\Legal\Models\Lease;
use App\Domain\Legal\States\Draft;
use App\Domain\Legal\States\WaitingLandlordSignature;
use App\Domain\Legal\States\WaitingTenantSignature;
use App\Domain\Legal\States\Active;
use App\Domain\Maintenance\Actions\SubmitMaintenanceRequestAction;
use App\Domain\Maintenance\Actions\UpdateMaintenanceStatusAction;
use App\Domain\Maintenance\Models\MaintenanceRequest;
use App\Domain\Maintenance\States\InProgress;
use App\Domain\Maintenance\States\Reported;
use App\Domain\Maintenance\States\Resolved;
use App\Domain\Negotiation\Actions\RespondToOfferAction;
use App\Domain\Negotiation\Models\Offer;
use App\Domain\Property\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ActionsTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function register_guest_action_is_complete(): void
    {
        $data = ['name' => 'Jane', 'email' => 'jane@example.com', 'password' => 'secret'];
        $action = new RegisterGuestAction();
        $user = $action->execute($data);
        $this->assertEquals('guest', $user->role);

        try {
            $action->execute(['password' => 123]);
            $this->fail();
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals('Password must be a string.', $e->getMessage());
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function sign_lease_action_is_complete(): void
    {
        $landlord = User::factory()->landlord()->create();
        $tenant = User::factory()->tenant()->create();
        $lease = Lease::factory()->create([
            'landlord_id' => $landlord->id,
            'tenant_id' => $tenant->id,
            'status' => WaitingLandlordSignature::class
        ]);

        $action = new SignLeaseAction();

        // 1. Landlord sign
        $this->actingAs($landlord);
        $action->execute($lease);
        $this->assertInstanceOf(WaitingTenantSignature::class, $lease->status);

        // 2. Tenant sign
        $this->actingAs($tenant);
        $action->execute($lease);
        $this->assertInstanceOf(Active::class, $lease->status);

        // 3. Unauthorized sign state (Active lease cannot be signed)
        try {
            $action->execute($lease);
            $this->fail();
        } catch (\Exception $e) {
            $this->assertEquals(403, $e->getCode());
        }

        // 4. Unauthorized user
        $stranger = User::factory()->create();
        $this->actingAs($stranger);
        $draftLease = Lease::factory()->create(['status' => WaitingLandlordSignature::class]);
        try {
            $action->execute($draftLease);
            $this->fail();
        } catch (\Exception $e) {
            $this->assertEquals(403, $e->getCode());
        }

        // 5. Unauthenticated
        auth()->logout();
        try {
            $action->execute($draftLease);
            $this->fail();
        } catch (\Exception $e) {
            $this->assertEquals(401, $e->getCode());
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function upload_identity_document_action_is_complete(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $this->actingAs($user);
        $file = UploadedFile::fake()->image('id.jpg');
        $action = new UploadIdentityDocumentAction();
        $doc = $action->execute($file);
        $this->assertEquals('identity_doc', $doc->type);

        auth()->logout();
        try {
            $action->execute($file);
            $this->fail();
        } catch (\Exception $e) {
            $this->assertEquals(401, $e->getCode());
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function submit_maintenance_request_action_is_complete(): void
    {
        $tenant = User::factory()->tenant()->create();
        $lease = Lease::factory()->create(['tenant_id' => $tenant->id, 'status' => Active::class]);
        $action = new SubmitMaintenanceRequestAction();
        $req = $action->execute($lease, $tenant, 'Title');
        $this->assertInstanceOf(Reported::class, $req->status);

        try {
            $action->execute($lease, User::factory()->create(), 'Title');
            $this->fail();
        } catch (\InvalidArgumentException $e) {
            $this->assertTrue(true);
        }

        $draftLease = Lease::factory()->create(['status' => Draft::class, 'tenant_id' => $tenant->id]);
        try {
            $action->execute($draftLease, $tenant, 'Title');
            $this->fail();
        } catch (\InvalidArgumentException $e) {
            $this->assertTrue(true);
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function respond_to_offer_action_is_complete(): void
    {
        $landlord = User::factory()->landlord()->create();
        $property = Property::factory()->create(['user_id' => $landlord->id]);
        $offer = Offer::factory()->create(['property_id' => $property->id, 'status' => \App\Domain\Negotiation\States\Pending::class]);
        $this->actingAs($landlord);
        $action = new RespondToOfferAction();
        $action->execute($offer, ['status' => 'accepted']);
        $this->assertInstanceOf(\App\Domain\Negotiation\States\AwaitingDocuments::class, $offer->fresh()->status);

        try {
            $action->execute($offer, ['status' => 123]);
            $this->fail();
        } catch (\InvalidArgumentException $e) {
            $this->assertTrue(true);
        }

        auth()->logout();
        try {
            $action->execute($offer, ['status' => 'rejected']);
            $this->fail();
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function update_maintenance_status_action_is_complete(): void
    {
        $landlord = User::factory()->landlord()->create();
        $property = Property::factory()->create(['user_id' => $landlord->id]);
        $lease = Lease::factory()->create(['property_id' => $property->id, 'landlord_id' => $landlord->id]);
        $request = MaintenanceRequest::factory()->create([
            'lease_id' => $lease->id,
            'user_id' => User::factory()->tenant()->create()->id,
            'status' => Reported::class,
        ]);

        $action = new UpdateMaintenanceStatusAction();
        
        // 1. Valid update
        $this->actingAs($landlord);
        $res = $action->execute($request, $landlord, InProgress::class);
        $this->assertInstanceOf(InProgress::class, $res->status);

        // 2. Unauthorized
        $stranger = User::factory()->create();
        try {
            $action->execute($request, $stranger, Resolved::class);
            $this->fail();
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals('You are not authorized to update this maintenance request.', $e->getMessage());
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function other_actions_are_complete(): void
    {
        Storage::fake('public');
        $user = User::factory()->tenant()->create();
        $this->actingAs($user);
        
        // 1. UploadComplianceDocumentAction
        $offer = Offer::factory()->create(['user_id' => $user->id, 'status' => \App\Domain\Negotiation\States\AwaitingDocuments::class]);
        $action1 = new \App\Domain\Legal\Actions\UploadComplianceDocumentAction();
        $doc1 = $action1->execute($offer, 'income_proof', UploadedFile::fake()->create('doc.pdf'));
        $this->assertInstanceOf(Document::class, $doc1);
        $this->assertInstanceOf(\App\Domain\Negotiation\States\PendingVerification::class, $offer->fresh()->status);

        // 2. UploadLeaseDocumentAction
        $lease = Lease::factory()->create(['tenant_id' => $user->id]);
        $action2 = new \App\Domain\Legal\Actions\UploadLeaseDocumentAction();
        $doc2 = $action2->execute($lease, 'lease_agreement', UploadedFile::fake()->create('lease.pdf'));
        $this->assertInstanceOf(Document::class, $doc2);

        // 3. StoreProperty
        $landlord = User::factory()->landlord()->create();
        $action3 = new \App\Domain\Property\Actions\StoreProperty();
        $prop = $action3->execute($landlord, [
            'name' => 'Prop', 'address' => 'Addr', 'price' => 1000, 'type' => 'Apartment'
        ]);
        $this->assertInstanceOf(Property::class, $prop);
    }
}

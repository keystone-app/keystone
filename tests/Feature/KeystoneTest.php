<?php

namespace Tests\Feature;

use App\Domain\Identity\Models\User;
use App\Domain\Legal\Models\Document;
use App\Domain\Legal\Models\Lease;
use App\Domain\Property\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KeystoneTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_create_a_landlord_with_properties(): void
    {
        $landlord = User::create([
            'name' => 'Alice Landlord',
            'email' => 'alice@example.com',
            'password' => bcrypt('password'),
            'role' => 'landlord',
        ]);

        $property = Property::create([
            'user_id' => $landlord->id,
            'name' => 'Modern Apartment 101',
            'address' => '123 Legal Lane',
            'status' => 'available',
        ]);

        $this->assertEquals('landlord', $landlord->role);
        $this->assertTrue($landlord->isLandlord());
        $property = $landlord->properties->first();
        $this->assertInstanceOf(Property::class, $property);

        $firstProperty = $landlord->properties->first();
        $this->assertInstanceOf(Property::class, $firstProperty);
        $this->assertEquals($property->id, $firstProperty->id);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_manage_leases_between_landlord_and_tenant(): void
    {
        $landlord = User::create([
            'name' => 'Alice Landlord',
            'email' => 'alice@example.com',
            'password' => bcrypt('password'),
            'role' => 'landlord',
        ]);

        $tenant = User::create([
            'name' => 'Bob Tenant',
            'email' => 'bob@example.com',
            'password' => bcrypt('password'),
            'role' => 'tenant',
        ]);

        $property = Property::create([
            'user_id' => $landlord->id,
            'name' => 'Modern Apartment 101',
            'address' => '123 Legal Lane',
            'status' => 'rented',
        ]);

        $lease = Lease::create([
            'property_id' => $property->id,
            'landlord_id' => $landlord->id,
            'tenant_id' => $tenant->id,
            'start_date' => now()->startOfMonth(),
            'rent_amount' => 2500.00,
            'status' => 'active',
        ]);

        $this->assertCount(1, $landlord->landlordLeases);
        $this->assertCount(1, $tenant->tenantLeases);
        $this->assertEquals($property->id, $lease->property->id);
        $this->assertEquals($landlord->id, $lease->landlord->id);
        $this->assertEquals($tenant->id, $lease->tenant->id);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_attach_documents_to_a_lease(): void
    {
        $landlord = User::create([
            'name' => 'Alice Landlord',
            'email' => 'alice@example.com',
            'password' => bcrypt('password'),
            'role' => 'landlord',
        ]);

        $tenant = User::create([
            'name' => 'Bob Tenant',
            'email' => 'bob@example.com',
            'password' => bcrypt('password'),
            'role' => 'tenant',
        ]);

        $property = Property::create([
            'user_id' => $landlord->id,
            'name' => 'Modern Apartment 101',
            'address' => '123 Legal Lane',
        ]);

        $lease = Lease::create([
            'property_id' => $property->id,
            'landlord_id' => $landlord->id,
            'tenant_id' => $tenant->id,
            'start_date' => now()->startOfMonth(),
            'rent_amount' => 2500.00,
        ]);

        $document = Document::create([
            'lease_id' => $lease->id,
            'name' => 'Lease Agreement',
            'path' => 'leases/001.pdf',
            'type' => 'lease_agreement',
        ]);

        $this->assertCount(1, $lease->documents);
        /** @var \App\Domain\Legal\Models\Document $firstDoc */
        $firstDoc = $lease->documents->first();
        $this->assertEquals($document->id, $firstDoc->id);
    }
}

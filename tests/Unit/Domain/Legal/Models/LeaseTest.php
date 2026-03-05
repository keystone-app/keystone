<?php

namespace Tests\Unit\Domain\Legal\Models;

use App\Domain\Identity\Models\User;
use App\Domain\Legal\Models\Document;
use App\Domain\Legal\Models\Lease;
use App\Domain\Property\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_lease_model_relationships(): void
    {
        $property = Property::factory()->create();
        $landlord = User::factory()->landlord()->create();
        $tenant = User::factory()->tenant()->create();
        
        $lease = Lease::factory()->create([
            'property_id' => $property->id,
            'landlord_id' => $landlord->id,
            'tenant_id' => $tenant->id,
        ]);

        $this->assertInstanceOf(Property::class, $lease->property);
        $this->assertEquals($property->id, $lease->property->id);
        
        $this->assertInstanceOf(User::class, $lease->landlord);
        $this->assertEquals($landlord->id, $lease->landlord->id);
        
        $this->assertInstanceOf(User::class, $lease->tenant);
        $this->assertEquals($tenant->id, $lease->tenant->id);

        Document::factory()->create(['lease_id' => $lease->id]);
        $this->assertCount(1, $lease->documents);
        $this->assertInstanceOf(Document::class, $lease->documents->first());
    }
}

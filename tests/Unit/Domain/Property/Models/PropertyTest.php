<?php

namespace Tests\Unit\Domain\Property\Models;

use App\Domain\Identity\Models\User;
use App\Domain\Legal\Models\Document;
use App\Domain\Legal\Models\Lease;
use App\Domain\Negotiation\Models\Offer;
use App\Domain\Property\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyTest extends TestCase
{
    use RefreshDatabase;

    public function test_property_model_relationships(): void
    {
        $user = User::factory()->create();
        $property = Property::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $property->user);
        $this->assertEquals($user->id, $property->user->id);

        $doc = Document::factory()->create([
            'documentable_id' => $property->id,
            'documentable_type' => Property::class
        ]);
        $this->assertCount(1, $property->media);
        $this->assertInstanceOf(Document::class, $property->media->first());

        $lease = Lease::factory()->create(['property_id' => $property->id]);
        $this->assertCount(1, $property->leases);
        $this->assertInstanceOf(Lease::class, $property->leases->first());

        $offer = Offer::factory()->create(['property_id' => $property->id]);
        $this->assertCount(1, $property->offers);
        $this->assertInstanceOf(Offer::class, $property->offers->first());
    }
}

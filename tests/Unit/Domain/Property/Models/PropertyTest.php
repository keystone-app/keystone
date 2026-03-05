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

    public function test_property_belongs_to_a_user(): void
    {
        $user = User::factory()->create();
        $property = Property::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $property->user);
        $this->assertEquals($user->id, $property->user->id);
    }

    public function test_property_has_media_relationship(): void
    {
        $property = Property::factory()->create();
        Document::factory()->create([
            'documentable_id' => $property->id,
            'documentable_type' => Property::class,
            'type' => 'image',
        ]);

        $this->assertCount(1, $property->media);
        $this->assertInstanceOf(Document::class, $property->media->first());
    }

    public function test_property_has_leases(): void
    {
        $property = Property::factory()->create();
        Lease::factory()->create(['property_id' => $property->id]);

        $this->assertCount(1, $property->leases);
    }

    public function test_property_has_offers(): void
    {
        $property = Property::factory()->create();
        Offer::factory()->create(['property_id' => $property->id]);

        $this->assertCount(1, $property->offers);
    }
}

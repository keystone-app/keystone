<?php

namespace Tests\Unit\Domain\Legal\Models;

use App\Domain\Identity\Models\User;
use App\Domain\Legal\Models\Document;
use App\Domain\Legal\Models\Lease;
use App\Domain\Negotiation\Models\Offer;
use App\Domain\Property\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentTest extends TestCase
{
    use RefreshDatabase;

    public function test_document_model_relationships(): void
    {
        $user = User::factory()->create();
        
        // User relationship
        $doc1 = Document::factory()->create(['user_id' => $user->id]);
        $this->assertInstanceOf(User::class, $doc1->user);
        $this->assertEquals($user->id, $doc1->user->id);

        // Lease relationship
        $lease = Lease::factory()->create();
        $doc2 = Document::factory()->create(['lease_id' => $lease->id]);
        $this->assertInstanceOf(Lease::class, $doc2->lease);
        $this->assertEquals($lease->id, $doc2->lease->id);

        // Polymorphic documentable (Offer)
        $offer = Offer::factory()->create();
        $doc3 = Document::factory()->create([
            'documentable_id' => $offer->id,
            'documentable_type' => Offer::class
        ]);
        $this->assertInstanceOf(Offer::class, $doc3->documentable);
        $this->assertEquals($offer->id, $doc3->documentable->id);

        // Polymorphic documentable (Property)
        $property = Property::factory()->create();
        $doc4 = Document::factory()->create([
            'documentable_id' => $property->id,
            'documentable_type' => Property::class
        ]);
        $this->assertInstanceOf(Property::class, $doc4->documentable);
        $this->assertEquals($property->id, $doc4->documentable->id);
    }
}

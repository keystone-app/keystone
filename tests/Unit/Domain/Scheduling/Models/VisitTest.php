<?php

namespace Tests\Unit\Domain\Scheduling\Models;

use App\Domain\Identity\Models\User;
use App\Domain\Legal\Models\Document;
use App\Domain\Negotiation\Models\Offer;
use App\Domain\Property\Models\Property;
use App\Domain\Scheduling\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisitTest extends TestCase
{
    use RefreshDatabase;

    public function test_visit_model_relationships(): void
    {
        $user = User::factory()->create();
        $property = Property::factory()->create();
        $doc = Document::factory()->create();
        
        $visit = Visit::factory()->create([
            'user_id' => $user->id,
            'property_id' => $property->id,
            'document_id' => $doc->id,
        ]);

        $this->assertInstanceOf(User::class, $visit->user);
        $this->assertEquals($user->id, $visit->user->id);
        $this->assertInstanceOf(Property::class, $visit->property);
        $this->assertEquals($property->id, $visit->property->id);
        $this->assertInstanceOf(Document::class, $visit->document);
        $this->assertEquals($doc->id, $visit->document->id);

        Offer::factory()->create(['visit_id' => $visit->id]);
        $this->assertInstanceOf(Offer::class, $visit->fresh()->offer);
    }
}

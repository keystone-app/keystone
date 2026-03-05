<?php

namespace Tests\Unit\Domain\Legal\Models;

use App\Domain\Identity\Models\User;
use App\Domain\Legal\Models\Document;
use App\Domain\Legal\Models\Lease;
use App\Domain\Negotiation\Models\Offer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentTest extends TestCase
{
    use RefreshDatabase;

    public function test_document_belongs_to_a_user(): void
    {
        $user = User::factory()->create();
        $document = Document::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $document->user);
        $this->assertEquals($user->id, $document->user->id);
    }

    public function test_document_belongs_to_a_lease(): void
    {
        $lease = Lease::factory()->create();
        $document = Document::factory()->create(['lease_id' => $lease->id]);

        $this->assertInstanceOf(Lease::class, $document->lease);
        $this->assertEquals($lease->id, $document->lease->id);
    }

    public function test_document_has_polymorphic_documentable_relationship(): void
    {
        $offer = Offer::factory()->create();
        $document = Document::factory()->create([
            'documentable_id' => $offer->id,
            'documentable_type' => Offer::class,
        ]);

        $this->assertInstanceOf(Offer::class, $document->documentable);
        $this->assertEquals($offer->id, $document->documentable->id);
    }
}

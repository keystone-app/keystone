<?php

namespace Tests\Feature;

use App\Domain\Identity\Models\User;
use App\Domain\Negotiation\Models\Offer;
use App\Domain\Property\Models\Property;
use App\Domain\Scheduling\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfferFlowTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function a_tenant_can_view_their_sent_offers()
    {
        $tenant = User::factory()->tenant()->create();
        $offer = Offer::factory()->create(['user_id' => $tenant->id]);
        $otherOffer = Offer::factory()->create();

        $response = $this->actingAs($tenant)->getJson('/offers');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['id' => $offer->id]);

        $ids = collect($response->json())->pluck('id');
        $this->assertNotContains($otherOffer->id, $ids);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function a_landlord_can_view_offers_for_their_properties()
    {
        $landlord = User::factory()->landlord()->create();
        $property = Property::factory()->create(['user_id' => $landlord->id]);
        $offer = Offer::factory()->create(['property_id' => $property->id]);

        $otherLandlord = User::factory()->landlord()->create();
        $otherOffer = Offer::factory()->create();

        $response = $this->actingAs($landlord)->getJson('/offers');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['id' => $offer->id]);

        $ids = collect($response->json())->pluck('id');
        $this->assertNotContains($otherOffer->id, $ids);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function a_tenant_can_make_an_offer_after_a_scheduled_visit()
    {
        $tenant = User::factory()->tenant()->create();
        $visit = Visit::factory()->create([
            'user_id' => $tenant->id,
            'status' => 'scheduled',
        ]);

        $offerData = [
            'visit_id' => $visit->id,
            'amount' => 2500,
            'terms' => 'Including parking space.',
        ];

        $response = $this->actingAs($tenant)->postJson('/offers', $offerData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('offers', [
            'user_id' => $tenant->id,
            'visit_id' => $visit->id,
            'amount' => 2500,
            'status' => 'pending',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function a_tenant_cannot_make_an_offer_for_a_visit_that_is_not_scheduled()
    {
        $tenant = User::factory()->tenant()->create();
        $visit = Visit::factory()->create([
            'user_id' => $tenant->id,
            'status' => 'pending',
        ]);

        $offerData = [
            'visit_id' => $visit->id,
            'amount' => 2500,
        ];

        $response = $this->actingAs($tenant)->postJson('/offers', $offerData);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('offers', ['visit_id' => $visit->id]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function a_landlord_can_accept_an_offer()
    {
        $landlord = User::factory()->landlord()->create();
        $property = Property::factory()->create(['user_id' => $landlord->id]);
        $offer = Offer::factory()->create([
            'property_id' => $property->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($landlord)->patchJson("/offers/{$offer->id}", [
            'status' => 'accepted',
        ]);

        $response->assertStatus(200);
        $this->assertEquals('accepted', $offer->fresh()->status);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function a_landlord_can_counter_an_offer()
    {
        $landlord = User::factory()->landlord()->create();
        $property = Property::factory()->create(['user_id' => $landlord->id]);
        $offer = Offer::factory()->create([
            'property_id' => $property->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($landlord)->patchJson("/offers/{$offer->id}", [
            'status' => 'countered',
            'amount' => 2700,
            'terms' => 'Rent increased to include utilities.',
        ]);

        $response->assertStatus(200);
        $this->assertEquals('countered', $offer->fresh()->status);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function a_landlord_cannot_respond_to_an_offer_for_someone_elses_property()
    {
        $landlord = User::factory()->landlord()->create();
        $otherLandlord = User::factory()->landlord()->create();
        $property = Property::factory()->create(['user_id' => $otherLandlord->id]);
        $offer = Offer::factory()->create([
            'property_id' => $property->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($landlord)->patchJson("/offers/{$offer->id}", [
            'status' => 'accepted',
        ]);

        $response->assertStatus(403);
        $this->assertEquals('pending', $offer->fresh()->status);
    }
}

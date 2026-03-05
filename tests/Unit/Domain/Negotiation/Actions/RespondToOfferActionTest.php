<?php

namespace Tests\Unit\Domain\Negotiation\Actions;

use App\Domain\Identity\Models\User;
use App\Domain\Negotiation\Actions\RespondToOfferAction;
use App\Domain\Negotiation\Models\Offer;
use App\Domain\Property\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RespondToOfferActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_throws_exception_if_status_is_not_string(): void
    {
        $landlord = User::factory()->landlord()->create();
        $property = Property::factory()->create(['user_id' => $landlord->id]);
        $offer = Offer::factory()->create(['property_id' => $property->id]);
        
        $this->actingAs($landlord);

        $action = new RespondToOfferAction();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Status must be a string.');

        $action->execute($offer, ['status' => 123]);
    }

    public function test_it_throws_exception_if_unauthorized(): void
    {
        $offer = Offer::factory()->create();
        $stranger = User::factory()->create();
        $this->actingAs($stranger);

        $action = new RespondToOfferAction();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unauthorized');

        $action->execute($offer, ['status' => 'accepted']);
    }

    public function test_it_can_accept_offer(): void
    {
        $landlord = User::factory()->landlord()->create();
        $property = Property::factory()->create(['user_id' => $landlord->id]);
        $offer = Offer::factory()->create(['property_id' => $property->id, 'status' => \App\Domain\Negotiation\States\Pending::class]);
        
        $this->actingAs($landlord);

        $action = new RespondToOfferAction();
        $result = $action->execute($offer, ['status' => 'accepted']);

        $this->assertInstanceOf(\App\Domain\Negotiation\States\AwaitingDocuments::class, $result->status);
        $this->assertDatabaseHas('leases', ['property_id' => $property->id]);
    }
}

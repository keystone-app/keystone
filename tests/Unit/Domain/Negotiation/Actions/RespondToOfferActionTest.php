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
}

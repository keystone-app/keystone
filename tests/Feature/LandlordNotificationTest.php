<?php

namespace Tests\Feature;

use App\Domain\Identity\Models\User;
use App\Domain\Negotiation\Actions\VerifyIncomeAction;
use App\Domain\Negotiation\Models\Offer;
use App\Domain\Negotiation\States\PendingVerification;
use App\Domain\Property\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class LandlordNotificationTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_notifies_the_landlord_when_an_offer_is_verified(): void
    {
        Notification::fake();

        $landlord = User::factory()->create(['role' => 'landlord']);
        $tenant = User::factory()->create(['role' => 'tenant']);
        
        $property = Property::factory()->create(['user_id' => $landlord->id]);
        
        $offer = Offer::factory()->create([
            'user_id' => $tenant->id,
            'property_id' => $property->id,
            'status' => PendingVerification::class,
        ]);

        $this->actingAs($tenant);

        $action = app(VerifyIncomeAction::class);
        $action->execute($offer);

        Notification::assertSentTo(
            $landlord,
            \App\Domain\Negotiation\Notifications\TenantVerifiedNotification::class,
            function ($notification) use ($offer) {
                return $notification->offer->id === $offer->id;
            }
        );
    }
}

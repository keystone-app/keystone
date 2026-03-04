<?php

namespace App\Domain\Negotiation\Actions;

use App\Domain\Legal\Actions\CreateLeaseFromOfferAction;
use App\Domain\Negotiation\Models\Offer;
use Illuminate\Support\Facades\Auth;

class RespondToOfferAction
{
    public function execute(Offer $offer, array $data): Offer
    {
        // Authorization: Ensure the offer belongs to a property owned by the authenticated landlord
        if ($offer->property->user_id !== Auth::id()) {
            throw new \Exception('Unauthorized', 403);
        }

        $offer->status->transitionTo($data['status']);

        // If accepted, move to compliance document phase and create lease draft
        if ($data['status'] === 'accepted') {
            $offer->status->transitionTo(\App\Domain\Negotiation\States\AwaitingDocuments::class);

            app(CreateLeaseFromOfferAction::class)->execute($offer);
        }

        return $offer->load(['user', 'property', 'visit']);
    }
}

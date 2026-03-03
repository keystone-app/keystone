<?php

namespace App\Domain\Negotiation\Actions;

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

        $offer->update(['status' => $data['status']]);

        // If accepted, move to compliance document phase
        if ($data['status'] === 'accepted') {
            $offer->update(['compliance_status' => 'awaiting_documents']);
        }

        return $offer->load(['user', 'property', 'visit']);
    }
}

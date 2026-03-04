<?php

namespace App\Domain\Legal\Actions;

use App\Domain\Legal\Models\Lease;
use App\Domain\Legal\States\Draft;
use App\Domain\Negotiation\Models\Offer;

class CreateLeaseFromOfferAction
{
    public function execute(Offer $offer): Lease
    {
        return Lease::create([
            'property_id' => $offer->property_id,
            'landlord_id' => $offer->property->user_id,
            'tenant_id' => $offer->user_id,
            'start_date' => now()->addDays(7), // Default start date in 7 days
            'rent_amount' => $offer->amount,
            'status' => Draft::class,
        ]);
    }
}

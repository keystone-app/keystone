<?php

namespace App\Domain\Negotiation\Actions;

use App\Domain\Negotiation\Models\Offer;
use App\Domain\Scheduling\Models\Visit;
use Illuminate\Support\Facades\Auth;

class SubmitOfferAction
{
    public function execute(array $data): Offer
    {
        $visit = Visit::findOrFail($data['visit_id']);

        // Business Rule: Ensure visit is scheduled and belongs to the user
        if ($visit->user_id !== Auth::id() || ! ($visit->status instanceof \App\Domain\Scheduling\States\Scheduled)) {
            throw new \Exception('Unauthorized or visit not scheduled.', 403);
        }

        return Offer::create([
            'user_id' => Auth::id(),
            'property_id' => $visit->property_id,
            'visit_id' => $visit->id,
            'amount' => $data['amount'],
            'terms' => $data['terms'],
            'status' => \App\Domain\Negotiation\States\Pending::class,
        ]);
    }
}

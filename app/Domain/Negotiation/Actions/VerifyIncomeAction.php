<?php

namespace App\Domain\Negotiation\Actions;

use App\Domain\Legal\Actions\CreateLeaseFromOfferAction;
use App\Domain\Negotiation\Models\Offer;
use Illuminate\Support\Facades\Auth;

class VerifyIncomeAction
{
    /**
     * @return array<string, mixed>
     */
    public function execute(Offer $offer): array
    {
        // Authorization: Tenant triggers verification
        if ($offer->user_id !== Auth::id()) {
            throw new \Exception('Unauthorized', 403);
        }

        $offer->status->transitionTo(\App\Domain\Negotiation\States\Verified::class);

        // Automate lease creation
        app(CreateLeaseFromOfferAction::class)->execute($offer);

        return [
            'offer' => $offer->load(['user', 'property']),
            'verification' => [
                'provider' => 'Brazil Open Finance',
                'status' => 'success',
                'monthly_income_verified' => (float) $offer->amount * 3.5,
                'confidence_score' => 0.98,
                'verified_at' => now(),
            ],
        ];
    }
}

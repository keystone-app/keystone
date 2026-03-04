<?php

namespace App\Domain\Legal\Actions;

use App\Domain\Legal\Models\Lease;
use App\Domain\Legal\States\Active;
use App\Domain\Legal\States\WaitingLandlordSignature;
use App\Domain\Legal\States\WaitingTenantSignature;
use Illuminate\Support\Facades\Auth;

class SignLeaseAction
{
    public function execute(Lease $lease): Lease
    {
        $user = Auth::user();

        if ($user->id === $lease->landlord_id && ($lease->status instanceof WaitingLandlordSignature)) {
            $lease->status->transitionTo(WaitingTenantSignature::class);
        } elseif ($user->id === $lease->tenant_id && ($lease->status instanceof WaitingTenantSignature)) {
            $lease->status->transitionTo(Active::class);
        } else {
            throw new \Exception('Unauthorized or invalid lease state for signing.', 403);
        }

        return $lease;
    }
}

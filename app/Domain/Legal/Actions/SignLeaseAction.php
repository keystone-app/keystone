<?php

namespace App\Domain\Legal\Actions;

use App\Domain\Identity\Models\User;
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

        if (! $user instanceof User) {
            abort(401, 'Unauthenticated.');
        }

        if ($user->id === $lease->landlord_id && ($lease->status instanceof WaitingLandlordSignature)) {
            $lease->status->transitionTo(WaitingTenantSignature::class);
        } elseif ($user->id === $lease->tenant_id && ($lease->status instanceof WaitingTenantSignature)) {
            $lease->status->transitionTo(Active::class);
        } else {
            abort(403, 'Unauthorized or invalid lease state for signing.');
        }

        return $lease;
    }
}

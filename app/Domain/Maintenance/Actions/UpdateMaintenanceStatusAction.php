<?php

namespace App\Domain\Maintenance\Actions;

use App\Domain\Identity\Models\User;
use App\Domain\Maintenance\Models\MaintenanceRequest;
use App\Domain\Maintenance\States\MaintenanceStatus;

class UpdateMaintenanceStatusAction
{
    /**
     * @param MaintenanceRequest $request
     * @param User $user
     * @param string|MaintenanceStatus $newStatus
     * @return MaintenanceRequest
     * @throws \InvalidArgumentException
     * @throws \Spatie\ModelStates\Exceptions\TransitionNotFound
     */
    public function execute(MaintenanceRequest $request, User $user, string|MaintenanceStatus $newStatus): MaintenanceRequest
    {
        // Only the landlord of the property associated with the lease can update the status
        if ($request->lease->landlord_id !== $user->id) {
            throw new \InvalidArgumentException('You are not authorized to update this maintenance request.');
        }

        $request->status->transitionTo($newStatus);

        return $request->fresh();
    }
}

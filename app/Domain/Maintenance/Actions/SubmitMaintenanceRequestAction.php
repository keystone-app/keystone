<?php

namespace App\Domain\Maintenance\Actions;

use App\Domain\Identity\Models\User;
use App\Domain\Legal\Models\Lease;
use App\Domain\Legal\States\Active;
use App\Domain\Maintenance\Models\MaintenanceRequest;
use App\Domain\Maintenance\States\Reported;

class SubmitMaintenanceRequestAction
{
    /**
     * @param Lease $lease
     * @param User $tenant
     * @param string $title
     * @param string|null $description
     * @return MaintenanceRequest
     * @throws \InvalidArgumentException
     */
    public function execute(Lease $lease, User $tenant, string $title, ?string $description = null): MaintenanceRequest
    {
        if (! $lease->status instanceof Active) {
            throw new \InvalidArgumentException('Maintenance requests can only be submitted for active leases.');
        }

        if ($lease->tenant_id !== $tenant->id) {
            throw new \InvalidArgumentException('You are not a tenant of this lease.');
        }

        return MaintenanceRequest::create([
            'lease_id' => $lease->id,
            'user_id' => $tenant->id,
            'title' => $title,
            'description' => $description,
            'status' => Reported::class,
        ]);
    }
}

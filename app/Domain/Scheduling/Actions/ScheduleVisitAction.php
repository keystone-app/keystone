<?php

namespace App\Domain\Scheduling\Actions;

use App\Domain\Scheduling\Models\Visit;
use Illuminate\Support\Facades\Auth;

class ScheduleVisitAction
{
    public function execute(array $data): Visit
    {
        $user = Auth::user();
        $docId = $data['document_id'] ?? $user->identity_document_id;

        if (! $docId) {
            throw new \Exception('Identity document required.', 422);
        }

        return Visit::create([
            'user_id' => $user->id,
            'property_id' => $data['property_id'],
            'document_id' => $docId,
            'visit_at' => $data['visit_at'],
            'status' => \App\Domain\Scheduling\States\Pending::class,
        ]);
    }
}

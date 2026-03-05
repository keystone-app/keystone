<?php

namespace App\Domain\Scheduling\Actions;

use App\Domain\Identity\Models\User;
use App\Domain\Scheduling\Models\Visit;
use Illuminate\Support\Facades\Auth;

class ScheduleVisitAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(array $data): Visit
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            abort(401, 'Unauthenticated.');
        }

        $docId = $data['document_id'] ?? $user->identity_document_id;

        if (! $docId) {
            abort(422, 'Identity document required.');
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

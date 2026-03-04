<?php

namespace App\Domain\Legal\Actions;

use App\Domain\Legal\Models\Document;
use App\Domain\Legal\Models\Lease;
use App\Domain\Legal\States\WaitingLandlordSignature;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

class UploadLeaseDocumentAction
{
    public function execute(Lease $lease, string $type, UploadedFile $file): Document
    {
        // Authorization: Ensure the user is either the landlord or the tenant of this lease
        if (Auth::id() !== $lease->landlord_id && Auth::id() !== $lease->tenant_id) {
            throw new \Exception('Unauthorized', 403);
        }

        $path = $file->store('lease_docs', 'public');

        $document = Document::create([
            'lease_id' => $lease->id,
            'user_id' => Auth::id(),
            'name' => $file->getClientOriginalName(),
            'path' => $path,
            'type' => $type,
        ]);

        // Check if both parties have uploaded at least one document
        $landlordHasDocs = Document::where('lease_id', $lease->id)->where('user_id', $lease->landlord_id)->exists();
        $tenantHasDocs = Document::where('lease_id', $lease->id)->where('user_id', $lease->tenant_id)->exists();

        if ($landlordHasDocs && $tenantHasDocs && ($lease->status instanceof \App\Domain\Legal\States\Draft)) {
            $lease->status->transitionTo(WaitingLandlordSignature::class);

            // Mock generating final lease agreement
            Document::create([
                'lease_id' => $lease->id,
                'user_id' => $lease->landlord_id,
                'name' => 'Final_Lease_Agreement.pdf',
                'path' => 'generated/lease_'.$lease->id.'.pdf',
                'type' => 'lease_agreement',
            ]);
        }

        return $document;
    }
}

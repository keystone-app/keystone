<?php

namespace App\Domain\Legal\Actions;

use App\Domain\Legal\Models\Document;
use App\Domain\Negotiation\Models\Offer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

class UploadComplianceDocumentAction
{
    public function execute(Offer $offer, string $type, UploadedFile $file): Document
    {
        // Authorization: Ensure the offer belongs to the authenticated user
        if ($offer->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $path = $file->store('compliance_docs', 'public');

        $document = Document::create([
            'user_id' => Auth::id(),
            'documentable_type' => Offer::class,
            'documentable_id' => $offer->id,
            'name' => $file->getClientOriginalName(),
            'path' => $path,
            'type' => $type,
        ]);

        // Check if both docs are now present for this specific offer to update offer status
        $hasIncome = Document::where('documentable_type', Offer::class)
            ->where('documentable_id', $offer->id)
            ->where('type', 'income_proof')
            ->exists();
            
        $hasResidency = Document::where('documentable_type', Offer::class)
            ->where('documentable_id', $offer->id)
            ->where('type', 'residency_proof')
            ->exists();

        if ($hasIncome && $hasResidency && ($offer->status instanceof \App\Domain\Negotiation\States\AwaitingDocuments)) {
            $offer->status->transitionTo(\App\Domain\Negotiation\States\PendingVerification::class);
        }

        return $document;
    }
}

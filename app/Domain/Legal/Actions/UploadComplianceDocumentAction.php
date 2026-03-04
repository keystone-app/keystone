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
            throw new \Exception('Unauthorized', 403);
        }

        $path = $file->store('compliance_docs', 'public');

        $document = Document::create([
            'user_id' => Auth::id(),
            'name' => $file->getClientOriginalName(),
            'path' => $path,
            'type' => $type,
        ]);

        // Check if both docs are now present to update offer status
        $hasIncome = Document::where('user_id', Auth::id())->where('type', 'income_proof')->exists();
        $hasResidency = Document::where('user_id', Auth::id())->where('type', 'residency_proof')->exists();

        if ($hasIncome && $hasResidency && ($offer->status instanceof \App\Domain\Negotiation\States\AwaitingDocuments)) {
            $offer->status->transitionTo(\App\Domain\Negotiation\States\PendingVerification::class);
        }

        return $document;
    }
}

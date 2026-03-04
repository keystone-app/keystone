<?php

namespace App\Http\Controllers;

use App\Domain\Legal\Actions\UploadComplianceDocumentAction;
use App\Domain\Legal\Actions\UploadIdentityDocumentAction;
use App\Domain\Negotiation\Models\Offer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function uploadIdentity(Request $request, UploadIdentityDocumentAction $action): JsonResponse
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        try {
            $document = $action->execute($request->file('file'));

            return response()->json($document);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function uploadCompliance(Request $request, UploadComplianceDocumentAction $action): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|max:10240',
            'type' => 'required|in:income_proof,residency_proof',
            'offer_id' => 'required|exists:offers,id',
        ]);

        $file = $request->file('file');
        if (! $file instanceof \Illuminate\Http\UploadedFile) {
            return response()->json(['message' => 'Invalid file.'], 400);
        }

        try {
            /** @var Offer $offer */
            $offer = Offer::findOrFail($request->integer('offer_id'));
            $document = $action->execute($offer, (string) $request->string('type'), $file);

            return response()->json($document);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 400);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

use App\Models\Offer;

class DocumentController extends Controller
{
    public function uploadIdentity(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $path = $request->file('file')->store('identity_docs', 'public');

        $document = Document::create([
            'user_id' => auth()->id(),
            'name' => $request->file('file')->getClientOriginalName(),
            'path' => $path,
            'type' => 'identity_doc',
        ]);

        auth()->user()->update([
            'identity_document_id' => $document->id,
        ]);

        return response()->json($document);
    }

    public function uploadCompliance(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
            'type' => 'required|in:income_proof,residency_proof',
            'offer_id' => 'required|exists:offers,id',
        ]);

        $offer = Offer::findOrFail($request->offer_id);

        if ($offer->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $path = $request->file('file')->store('compliance_docs', 'public');

        $document = Document::create([
            'user_id' => auth()->id(),
            'name' => $request->file('file')->getClientOriginalName(),
            'path' => $path,
            'type' => $request->type,
        ]);

        // Check if both docs are now present to update offer status
        $hasIncome = Document::where('user_id', auth()->id())->where('type', 'income_proof')->exists();
        $hasResidency = Document::where('user_id', auth()->id())->where('type', 'residency_proof')->exists();

        if ($hasIncome && $hasResidency && $offer->compliance_status === 'awaiting_documents') {
            $offer->update(['compliance_status' => 'pending_verification']);
        }

        return response()->json($document);
    }
}

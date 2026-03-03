<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use Illuminate\Http\Request;

class VisitController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'document_id' => 'nullable|exists:documents,id',
            'visit_at' => 'required|date',
        ]);

        $user = auth()->user();
        $docId = $data['document_id'] ?? $user->identity_document_id;

        if (!$docId) {
            return response()->json(['message' => 'Identity document required.'], 422);
        }

        $visit = Visit::create([
            'user_id' => $user->id,
            'property_id' => $data['property_id'],
            'document_id' => $docId,
            'visit_at' => $data['visit_at'],
            'status' => 'pending',
        ]);

        return response()->json($visit);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use Illuminate\Http\Request;

class VisitController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Get visits for properties owned by this landlord
        $visits = Visit::whereHas('property', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->with(['user', 'property', 'document'])
        ->latest()
        ->get();

        return response()->json($visits);
    }

    public function myVisits()
    {
        $visits = Visit::where('user_id', auth()->id())
            ->with(['property', 'document'])
            ->latest()
            ->get();

        return response()->json($visits);
    }

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

        return response()->json($visit->load(['user', 'property', 'document']));
    }

    public function update(Request $request, Visit $visit)
    {
        // Ensure the visit belongs to a property owned by the authenticated user
        if ($visit->property->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'status' => 'required|in:scheduled,cancelled,rejected',
        ]);

        $visit->update(['status' => $data['status']]);

        return response()->json($visit->load(['user', 'property', 'document']));
    }
}

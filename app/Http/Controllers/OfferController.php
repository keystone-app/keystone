<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\Visit;
use App\Models\Lease;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'landlord') {
            // Offers for properties owned by this landlord
            $offers = Offer::whereHas('property', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['user', 'property', 'visit'])
            ->latest()
            ->get();
        } else {
            // Offers made by this tenant/guest
            $offers = Offer::where('user_id', $user->id)
                ->with(['property', 'visit'])
                ->latest()
                ->get();
        }

        return response()->json($offers);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'visit_id' => 'required|exists:visits,id',
            'amount' => 'required|numeric|min:0',
            'terms' => 'nullable|string',
        ]);

        $visit = Visit::findOrFail($data['visit_id']);

        // Ensure visit is scheduled and belongs to the user
        if ($visit->user_id !== auth()->id() || $visit->status !== 'scheduled') {
            return response()->json(['message' => 'Unauthorized or visit not scheduled.'], 403);
        }

        $offer = Offer::create([
            'user_id' => auth()->id(),
            'property_id' => $visit->property_id,
            'visit_id' => $visit->id,
            'amount' => $data['amount'],
            'terms' => $data['terms'],
            'status' => 'pending',
        ]);

        return response()->json($offer->load(['property', 'visit']));
    }

    public function update(Request $request, Offer $offer)
    {
        // Ensure the offer belongs to a property owned by the authenticated landlord
        if ($offer->property->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'status' => 'required|in:accepted,rejected,countered',
            'amount' => 'nullable|numeric|min:0', // for counter offer
            'terms' => 'nullable|string', // for counter offer
        ]);

        $offer->update(['status' => $data['status']]);

        // If accepted, move to compliance document phase
        if ($data['status'] === 'accepted') {
            $offer->update(['compliance_status' => 'awaiting_documents']);
        }

        return response()->json($offer->load(['user', 'property', 'visit']));
    }

    public function verify(Request $request, Offer $offer)
    {
        // Tenant triggers verification after uploading docs
        if ($offer->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Mock Open Finance logic:
        // In a real app, we'd call a provider here.
        // For now, we just update the status to pending_verification then verified.
        
        $offer->update(['compliance_status' => 'verified']);

        return response()->json([
            'offer' => $offer->load(['user', 'property']),
            'verification' => [
                'provider' => 'Brazil Open Finance',
                'status' => 'success',
                'monthly_income_verified' => (float)$offer->amount * 3.5, // Mocked 3.5x rent
                'confidence_score' => 0.98,
                'verified_at' => now(),
            ]
        ]);
    }
}

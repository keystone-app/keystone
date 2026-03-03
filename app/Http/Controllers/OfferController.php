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

        // If accepted, we could trigger lease drafting here
        if ($data['status'] === 'accepted') {
            // Implementation for lease drafting could go here
        }

        return response()->json($offer->load(['user', 'property', 'visit']));
    }
}

<?php

namespace App\Http\Controllers;

use App\Domain\Negotiation\Actions\RespondToOfferAction;
use App\Domain\Negotiation\Actions\SubmitOfferAction;
use App\Domain\Negotiation\Actions\VerifyIncomeAction;
use App\Domain\Negotiation\Models\Offer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    public function index(): JsonResponse
    {
        $user = auth()->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if ($user->role === 'landlord') {
            $offers = Offer::whereHas('property', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
                ->with(['user', 'property', 'visit'])
                ->latest()
                ->get();
        } else {
            $offers = Offer::where('user_id', $user->id)
                ->with(['property', 'visit'])
                ->latest()
                ->get();
        }

        return response()->json($offers);
    }

    public function store(Request $request, SubmitOfferAction $action): JsonResponse
    {
        $data = $request->validate([
            'visit_id' => 'required|exists:visits,id',
            'amount' => 'required|numeric|min:0',
            'terms' => 'nullable|string',
        ]);

        try {
            $offer = $action->execute($data);

            return response()->json($offer->load(['property', 'visit']));
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 400);
        }
    }

    public function update(Request $request, Offer $offer, RespondToOfferAction $action): JsonResponse
    {
        $data = $request->validate([
            'status' => 'required|in:accepted,rejected,countered',
            'amount' => 'nullable|numeric|min:0',
            'terms' => 'nullable|string',
        ]);

        try {
            $offer = $action->execute($offer, $data);

            return response()->json($offer);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 400);
        }
    }

    public function verify(Request $request, Offer $offer, VerifyIncomeAction $action): JsonResponse
    {
        try {
            $result = $action->execute($offer);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 400);
        }
    }
}

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
            'amount' => 'required|numeric',
            'terms' => 'nullable|string',
        ]);

        try {
            $offer = $action->execute($data);

            return response()->json($offer, 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }
    }

    public function respond(Request $request, Offer $offer, RespondToOfferAction $action): JsonResponse
    {
        $data = $request->validate([
            'status' => 'required|in:accepted,rejected,countered',
            'amount' => 'nullable|numeric',
            'terms' => 'nullable|string',
        ]);

        try {
            $offer = $action->execute($offer, $data);

            return response()->json($offer);
        } catch (\Exception $e) {
            $code = $e->getCode();
            if ($code < 100 || $code >= 600) $code = 403;
            abort($code, $e->getMessage());
        }
    }

    public function verify(Request $request, Offer $offer, VerifyIncomeAction $action): JsonResponse
    {
        $result = $action->execute($offer);

        return response()->json($result);
    }
}

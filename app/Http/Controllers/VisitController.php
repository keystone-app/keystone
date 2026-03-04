<?php

namespace App\Http\Controllers;

use App\Domain\Scheduling\Actions\ScheduleVisitAction;
use App\Domain\Scheduling\Models\Visit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VisitController extends Controller
{
    public function index(): JsonResponse
    {
        $user = auth()->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Get visits for properties owned by this landlord
        $visits = Visit::whereHas('property', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->with(['user', 'property', 'document'])
            ->latest()
            ->get();

        return response()->json($visits);
    }

    public function myVisits(): JsonResponse
    {
        $visits = Visit::where('user_id', auth()->id())
            ->with(['property', 'document'])
            ->latest()
            ->get();

        return response()->json($visits);
    }

    public function store(Request $request, ScheduleVisitAction $action): JsonResponse
    {
        $data = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'document_id' => 'nullable|exists:documents,id',
            'visit_at' => 'required|date',
        ]);

        try {
            $visit = $action->execute($data);

            return response()->json($visit->load(['user', 'property', 'document']));
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 400);
        }
    }

    public function update(Request $request, Visit $visit): JsonResponse
    {
        // Ensure the visit belongs to a property owned by the authenticated user
        if ($visit->property->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'status' => 'required|in:scheduled,cancelled,rejected',
        ]);

        $visit->status->transitionTo($data['status']);

        return response()->json($visit->load(['user', 'property', 'document']));
    }
}

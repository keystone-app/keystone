<?php

namespace App\Http\Controllers;

use App\Domain\Legal\Models\Lease;
use App\Domain\Maintenance\Actions\SubmitMaintenanceRequestAction;
use App\Domain\Maintenance\Actions\UpdateMaintenanceStatusAction;
use App\Domain\Maintenance\Models\MaintenanceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function index(): JsonResponse
    {
        $user = auth()->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if ($user->role === 'landlord') {
            $requests = MaintenanceRequest::whereHas('lease', function ($query) use ($user) {
                $query->where('landlord_id', $user->id);
            })->with('lease.property')->latest()->get();
        } else {
            $requests = MaintenanceRequest::where('user_id', $user->id)
                ->with('lease.property')
                ->latest()
                ->get();
        }

        return response()->json($requests);
    }

    public function store(Request $request, SubmitMaintenanceRequestAction $action): JsonResponse
    {
        $data = $request->validate([
            'lease_id' => 'required|exists:leases,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $lease = Lease::findOrFail($data['lease_id']);
        $user = auth()->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $maintenanceRequest = $action->execute(
            $lease,
            $user,
            $data['title'],
            $data['description'] ?? null
        );

        return response()->json($maintenanceRequest, 201);
    }

    public function update(Request $request, MaintenanceRequest $maintenanceRequest, UpdateMaintenanceStatusAction $action): JsonResponse
    {
        $data = $request->validate([
            'status' => 'required|string',
        ]);

        $user = auth()->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Simple authorization check before calling the action
        // The action also has an authorization check, but we can catch it here too
        if ($user->role !== 'landlord') {
             return response()->json(['message' => 'Only landlords can update maintenance status.'], 403);
        }

        try {
            $updatedRequest = $action->execute(
                $maintenanceRequest,
                $user,
                $data['status']
            );

            return response()->json($updatedRequest);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        } catch (\Spatie\ModelStates\Exceptions\TransitionNotFound $e) {
            return response()->json(['message' => 'Invalid status transition.'], 422);
        }
    }
}

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
        /** @var \App\Domain\Identity\Models\User $user */
        $user = auth()->user();

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
        /** @var \App\Domain\Identity\Models\User $user */
        $user = auth()->user();

        try {
            $maintenanceRequest = $action->execute(
                $lease,
                $user,
                $data['title'],
                $data['description'] ?? null
            );

            return response()->json($maintenanceRequest, 201);
        } catch (\Exception $e) {
            abort(400, $e->getMessage());
        }
    }

    public function update(Request $request, MaintenanceRequest $maintenanceRequest, UpdateMaintenanceStatusAction $action): JsonResponse
    {
        $data = $request->validate([
            'status' => 'required|string',
        ]);

        /** @var \App\Domain\Identity\Models\User $user */
        $user = auth()->user();

        try {
            $updatedRequest = $action->execute(
                $maintenanceRequest,
                $user,
                $data['status']
            );

            return response()->json($updatedRequest);
        } catch (\Exception $e) {
            $code = $e->getCode();
            if ($code < 100 || $code >= 600) $code = 403;
            abort($code, $e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Domain\Legal\Actions\SignLeaseAction;
use App\Domain\Legal\Actions\UploadLeaseDocumentAction;
use App\Domain\Legal\Models\Lease;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeaseController extends Controller
{
    public function index(): JsonResponse
    {
        $user = auth()->user();

        $leases = Lease::where('landlord_id', $user->id)
            ->orWhere('tenant_id', $user->id)
            ->with(['property', 'landlord', 'tenant', 'documents'])
            ->latest()
            ->get();

        return response()->json($leases);
    }

    public function uploadDocument(Request $request, Lease $lease, UploadLeaseDocumentAction $action): JsonResponse
    {
        $request->validate([
            'type' => 'required|string',
            'file' => 'required|file|max:10240',
        ]);

        $document = $action->execute($lease, $request->type, $request->file('file'));

        return response()->json($document);
    }

    public function sign(Request $request, Lease $lease, SignLeaseAction $action): JsonResponse
    {
        $lease = $action->execute($lease);

        return response()->json($lease->load(['property', 'landlord', 'tenant', 'documents']));
    }
}

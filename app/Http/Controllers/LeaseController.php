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
        /** @var \App\Domain\Identity\Models\User $user */
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

        $file = $request->file('file');

        try {
            $document = $action->execute($lease, (string) $request->string('type'), $file);

            return response()->json($document);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }
    }

    public function sign(Request $request, Lease $lease, SignLeaseAction $action): JsonResponse
    {
        try {
            $lease = $action->execute($lease);

            return response()->json($lease->load(['property', 'landlord', 'tenant', 'documents']));
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }
    }
}

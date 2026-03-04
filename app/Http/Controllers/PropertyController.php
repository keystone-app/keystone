<?php

namespace App\Http\Controllers;

use App\Domain\Property\Actions\StoreProperty;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function store(Request $request, StoreProperty $storeProperty): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $property = $storeProperty->execute($user, $request->all());

        return response()->json($property, 201);
    }

    public function index(): JsonResponse
    {
        return response()->json(\App\Domain\Property\Models\Property::all());
    }
}

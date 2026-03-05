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

    public function index(Request $request): JsonResponse
    {
        $query = \App\Domain\Property\Models\Property::query()->with('media');

        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('status')) {
            $query->whereState('status', $request->status);
        }

        return response()->json($query->get());
    }
}

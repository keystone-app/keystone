<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use Illuminate\Http\Request;

class VisitController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'document_id' => 'required|exists:documents,id',
            'visit_at' => 'required|date',
        ]);

        $visit = Visit::create([
            'user_id' => auth()->id(),
            'property_id' => $data['property_id'],
            'document_id' => $data['document_id'],
            'visit_at' => $data['visit_at'],
            'status' => 'pending',
        ]);

        return response()->json($visit);
    }
}

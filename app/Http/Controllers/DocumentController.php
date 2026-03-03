<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function uploadIdentity(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $path = $request->file('file')->store('identity_docs', 'public');

        $document = Document::create([
            'user_id' => auth()->id(),
            'name' => $request->file('file')->getClientOriginalName(),
            'path' => $path,
            'type' => 'identity_doc',
        ]);

        return response()->json($document);
    }
}

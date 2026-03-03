<?php

namespace App\Domain\Legal\Actions;

use App\Domain\Legal\Models\Document;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

class UploadIdentityDocumentAction
{
    public function execute(UploadedFile $file): Document
    {
        $path = $file->store('identity_docs', 'public');

        $document = Document::create([
            'user_id' => Auth::id(),
            'name' => $file->getClientOriginalName(),
            'path' => $path,
            'type' => 'identity_doc',
        ]);

        Auth::user()->update([
            'identity_document_id' => $document->id,
        ]);

        return $document;
    }
}

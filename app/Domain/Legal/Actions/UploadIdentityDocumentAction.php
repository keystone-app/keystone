<?php

namespace App\Domain\Legal\Actions;

use App\Domain\Identity\Models\User;
use App\Domain\Legal\Models\Document;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

class UploadIdentityDocumentAction
{
    public function execute(UploadedFile $file): Document
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            throw new \Exception('Unauthenticated.', 401);
        }

        $path = $file->store('identity_docs', 'public');

        $document = Document::create([
            'user_id' => $user->id,
            'name' => $file->getClientOriginalName(),
            'path' => $path,
            'type' => 'identity_doc',
        ]);

        $user->update([
            'identity_document_id' => $document->id,
        ]);

        return $document;
    }
}

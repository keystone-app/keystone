<?php

namespace Tests\Unit\Domain\Legal\Actions;

use App\Domain\Identity\Models\User;
use App\Domain\Legal\Actions\UploadIdentityDocumentAction;
use App\Domain\Legal\Models\Document;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UploadIdentityDocumentActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_upload_an_identity_document(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $this->actingAs($user);

        $file = UploadedFile::fake()->image('id_card.jpg');

        $action = new UploadIdentityDocumentAction();
        $document = $action->execute($file);

        $this->assertInstanceOf(Document::class, $document);
        $this->assertEquals($user->id, $document->user_id);
        $this->assertEquals('id_card.jpg', $document->name);
        $this->assertEquals('identity_doc', $document->type);
        
        Storage::disk('public')->assertExists($document->path);

        $this->assertEquals($document->id, $user->fresh()->identity_document_id);
    }

    public function test_it_throws_exception_if_not_authenticated(): void
    {
        $file = UploadedFile::fake()->image('id_card.jpg');
        $action = new UploadIdentityDocumentAction();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unauthenticated.');

        $action->execute($file);
    }
}

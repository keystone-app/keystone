<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Property;
use App\Models\Visit;
use App\Models\Document;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisitControllerTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function a_landlord_can_fetch_visit_requests_for_their_properties()
    {
        $landlord = User::factory()->landlord()->create();
        $otherLandlord = User::factory()->landlord()->create();

        $property = Property::factory()->create(['user_id' => $landlord->id]);
        $otherProperty = Property::factory()->create(['user_id' => $otherLandlord->id]);

        $visit = Visit::factory()->create(['property_id' => $property->id]);
        $otherVisit = Visit::factory()->create(['property_id' => $otherProperty->id]);

        $response = $this->actingAs($landlord)->getJson('/visits');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['id' => $visit->id]);
        
        $ids = collect($response->json())->pluck('id');
        $this->assertNotContains($otherVisit->id, $ids);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function a_user_can_schedule_a_visit_with_an_identity_document()
    {
        $user = User::factory()->tenant()->create();
        $property = Property::factory()->create();
        $document = Document::factory()->create(['user_id' => $user->id]);

        $visitData = [
            'property_id' => $property->id,
            'document_id' => $document->id,
            'visit_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
        ];

        $response = $this->actingAs($user)->postJson('/visits', $visitData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('visits', [
            'user_id' => $user->id,
            'property_id' => $property->id,
            'document_id' => $document->id,
            'status' => 'pending',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function a_user_can_schedule_a_visit_using_their_stored_identity_document()
    {
        $user = User::factory()->tenant()->create();
        $document = Document::factory()->create(['user_id' => $user->id]);
        $user->update(['identity_document_id' => $document->id]);

        $property = Property::factory()->create();

        $visitData = [
            'property_id' => $property->id,
            'visit_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
        ];

        $response = $this->actingAs($user)->postJson('/visits', $visitData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('visits', [
            'user_id' => $user->id,
            'property_id' => $property->id,
            'document_id' => $document->id,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function a_landlord_can_approve_a_visit_request()
    {
        $landlord = User::factory()->landlord()->create();
        $property = Property::factory()->create(['user_id' => $landlord->id]);
        $visit = Visit::factory()->create(['property_id' => $property->id, 'status' => 'pending']);

        $response = $this->actingAs($landlord)->patchJson("/visits/{$visit->id}", [
            'status' => 'scheduled',
        ]);

        $response->assertStatus(200);
        $this->assertEquals('scheduled', $visit->fresh()->status);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function a_landlord_cannot_update_a_visit_for_a_property_they_do_not_own()
    {
        $landlord = User::factory()->landlord()->create();
        $otherLandlord = User::factory()->landlord()->create();
        $property = Property::factory()->create(['user_id' => $otherLandlord->id]);
        $visit = Visit::factory()->create(['property_id' => $property->id, 'status' => 'pending']);

        $response = $this->actingAs($landlord)->patchJson("/visits/{$visit->id}", [
            'status' => 'scheduled',
        ]);

        $response->assertStatus(403);
        $this->assertEquals('pending', $visit->fresh()->status);
    }
}

<?php

namespace Tests\Feature;

use App\Domain\Identity\Models\User;
use App\Domain\Legal\Models\Lease;
use App\Domain\Legal\States\Active;
use App\Domain\Maintenance\Models\MaintenanceRequest;
use App\Domain\Maintenance\States\InProgress;
use App\Domain\Maintenance\States\Reported;
use App\Domain\Maintenance\States\Resolved;
use App\Domain\Property\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaintenanceControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_can_list_their_maintenance_requests(): void
    {
        $tenant = User::factory()->tenant()->create();
        $lease = Lease::factory()->create(['tenant_id' => $tenant->id, 'status' => Active::class]);
        
        MaintenanceRequest::create([
            'lease_id' => $lease->id,
            'user_id' => $tenant->id,
            'title' => 'Leaking tap',
            'status' => Reported::class,
        ]);

        $response = $this->actingAs($tenant)->getJson('/maintenance');

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonPath('0.title', 'Leaking tap');
    }

    public function test_landlord_can_list_all_maintenance_requests_for_their_properties(): void
    {
        $landlord = User::factory()->landlord()->create();
        $property = Property::factory()->create(['user_id' => $landlord->id]);
        $lease = Lease::factory()->create(['property_id' => $property->id, 'landlord_id' => $landlord->id, 'status' => Active::class]);
        
        $tenant = User::factory()->tenant()->create();
        MaintenanceRequest::create([
            'lease_id' => $lease->id,
            'user_id' => $tenant->id,
            'title' => 'Leaking tap',
            'status' => Reported::class,
        ]);

        $response = $this->actingAs($landlord)->getJson('/maintenance');

        $response->assertStatus(200)
            ->assertJsonCount(1);
    }

    public function test_tenant_can_submit_maintenance_request(): void
    {
        $tenant = User::factory()->tenant()->create();
        $lease = Lease::factory()->create(['tenant_id' => $tenant->id, 'status' => Active::class]);

        $response = $this->actingAs($tenant)->postJson('/maintenance', [
            'lease_id' => $lease->id,
            'title' => 'Leaking tap',
            'description' => 'The kitchen tap is leaking.',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('title', 'Leaking tap');
    }

    public function test_tenant_cannot_submit_maintenance_request_for_others_lease(): void
    {
        $tenant = User::factory()->tenant()->create();
        $otherLease = Lease::factory()->create(['status' => Active::class]);
        
        $response = $this->actingAs($tenant)->postJson('/maintenance', [
            'lease_id' => $otherLease->id,
            'title' => 'Test',
        ]);

        $response->assertStatus(400);
    }

    public function test_landlord_can_update_maintenance_request_status(): void
    {
        $landlord = User::factory()->landlord()->create();
        $property = Property::factory()->create(['user_id' => $landlord->id]);
        $lease = Lease::factory()->create(['property_id' => $property->id, 'landlord_id' => $landlord->id, 'status' => Active::class]);
        
        $request = MaintenanceRequest::create([
            'lease_id' => $lease->id,
            'user_id' => User::factory()->tenant()->create()->id,
            'title' => 'Leaking tap',
            'status' => Reported::class,
        ]);

        $response = $this->actingAs($landlord)->patchJson("/maintenance/{$request->id}", [
            'status' => 'in_progress',
        ]);

        $response->assertStatus(200);
    }

    public function test_update_handles_invalid_transitions(): void
    {
        $landlord = User::factory()->landlord()->create();
        $property = Property::factory()->create(['user_id' => $landlord->id]);
        $lease = Lease::factory()->create(['property_id' => $property->id, 'landlord_id' => $landlord->id, 'status' => Active::class]);
        
        $request = MaintenanceRequest::create([
            'lease_id' => $lease->id,
            'user_id' => User::factory()->tenant()->create()->id,
            'title' => 'Leaking tap',
            'status' => Reported::class,
        ]);

        // Reported -> Resolved is invalid
        $response = $this->actingAs($landlord)->patchJson("/maintenance/{$request->id}", [
            'status' => 'resolved',
        ]);

        $response->assertStatus(403);
    }

    public function test_index_requires_authentication(): void
    {
        $response = $this->getJson('/maintenance');
        $response->assertStatus(401);
    }

    public function test_update_handles_invalid_transitions(): void
    {
        $landlord = User::factory()->landlord()->create();
        $property = Property::factory()->create(['user_id' => $landlord->id]);
        $lease = Lease::factory()->create(['property_id' => $property->id, 'landlord_id' => $landlord->id, 'status' => Active::class]);

        $request = MaintenanceRequest::create([
            'lease_id' => $lease->id,
            'user_id' => User::factory()->tenant()->create()->id,
            'title' => 'Leaking tap',
            'status' => Reported::class,
        ]);

        // Reported -> Resolved is invalid
        $response = $this->actingAs($landlord)->patchJson("/maintenance/{$request->id}", [
            'status' => 'resolved',
        ]);

        $response->assertStatus(422);
    }
}

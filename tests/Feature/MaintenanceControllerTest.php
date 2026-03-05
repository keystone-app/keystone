<?php

namespace Tests\Feature;

use App\Domain\Identity\Models\User;
use App\Domain\Legal\Models\Lease;
use App\Domain\Legal\States\Active;
use App\Domain\Maintenance\Models\MaintenanceRequest;
use App\Domain\Maintenance\States\InProgress;
use App\Domain\Maintenance\States\Reported;
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

        $otherTenant = User::factory()->tenant()->create();
        $otherLease = Lease::factory()->create(['tenant_id' => $otherTenant->id, 'status' => Active::class]);
        MaintenanceRequest::create([
            'lease_id' => $otherLease->id,
            'user_id' => $otherTenant->id,
            'title' => 'Broken window',
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

        $otherLandlord = User::factory()->landlord()->create();
        $otherProperty = Property::factory()->create(['user_id' => $otherLandlord->id]);
        $otherLease = Lease::factory()->create(['property_id' => $otherProperty->id, 'landlord_id' => $otherLandlord->id, 'status' => Active::class]);
        MaintenanceRequest::create([
            'lease_id' => $otherLease->id,
            'user_id' => User::factory()->tenant()->create()->id,
            'title' => 'Broken window',
            'status' => Reported::class,
        ]);

        $response = $this->actingAs($landlord)->getJson('/maintenance');

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonPath('0.title', 'Leaking tap');
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
            ->assertJsonPath('title', 'Leaking tap')
            ->assertJsonPath('status', 'reported');

        $this->assertDatabaseHas('maintenance_requests', [
            'lease_id' => $lease->id,
            'user_id' => $tenant->id,
            'title' => 'Leaking tap',
        ]);
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

        $response->assertStatus(200)
            ->assertJsonPath('status', 'in_progress');

        $this->assertDatabaseHas('maintenance_requests', [
            'id' => $request->id,
            'status' => 'in_progress',
        ]);
    }

    public function test_tenant_cannot_update_maintenance_request_status(): void
    {
        $tenant = User::factory()->tenant()->create();
        $lease = Lease::factory()->create(['tenant_id' => $tenant->id, 'status' => Active::class]);
        
        $request = MaintenanceRequest::create([
            'lease_id' => $lease->id,
            'user_id' => $tenant->id,
            'title' => 'Leaking tap',
            'status' => Reported::class,
        ]);

        $response = $this->actingAs($tenant)->patchJson("/maintenance/{$request->id}", [
            'status' => 'in_progress',
        ]);

        $response->assertStatus(403);
    }
}

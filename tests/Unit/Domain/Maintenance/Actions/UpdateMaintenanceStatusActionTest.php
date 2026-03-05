<?php

namespace Tests\Unit\Domain\Maintenance\Actions;

use App\Domain\Identity\Models\User;
use App\Domain\Legal\Models\Lease;
use App\Domain\Maintenance\Actions\UpdateMaintenanceStatusAction;
use App\Domain\Maintenance\Models\MaintenanceRequest;
use App\Domain\Maintenance\States\InProgress;
use App\Domain\Maintenance\States\Reported;
use App\Domain\Maintenance\States\Resolved;
use App\Domain\Property\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\ModelStates\Exceptions\TransitionNotFound;
use Tests\TestCase;

class UpdateMaintenanceStatusActionTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function a_landlord_can_update_the_status_of_a_maintenance_request(): void
    {
        $landlord = User::factory()->landlord()->create();
        $property = Property::factory()->create(['user_id' => $landlord->id]);
        $lease = Lease::factory()->create(['property_id' => $property->id, 'landlord_id' => $landlord->id]);
        $request = MaintenanceRequest::create([
            'lease_id' => $lease->id,
            'user_id' => User::factory()->tenant()->create()->id,
            'title' => 'Leaking tap',
            'status' => Reported::class,
        ]);

        $action = new UpdateMaintenanceStatusAction();
        $updatedRequest = $action->execute($request, $landlord, InProgress::class);

        $this->assertInstanceOf(InProgress::class, $updatedRequest->status);
        $this->assertDatabaseHas('maintenance_requests', [
            'id' => $request->id,
            'status' => 'in_progress',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function a_non_landlord_cannot_update_the_status(): void
    {
        $landlord = User::factory()->landlord()->create();
        $property = Property::factory()->create(['user_id' => $landlord->id]);
        $lease = Lease::factory()->create(['property_id' => $property->id, 'landlord_id' => $landlord->id]);
        $request = MaintenanceRequest::create([
            'lease_id' => $lease->id,
            'user_id' => User::factory()->tenant()->create()->id,
            'title' => 'Leaking tap',
            'status' => Reported::class,
        ]);

        $otherUser = User::factory()->create();
        $action = new UpdateMaintenanceStatusAction();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('You are not authorized to update this maintenance request.');

        $action->execute($request, $otherUser, InProgress::class);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function invalid_state_transitions_are_prevented(): void
    {
        $landlord = User::factory()->landlord()->create();
        $property = Property::factory()->create(['user_id' => $landlord->id]);
        $lease = Lease::factory()->create(['property_id' => $property->id, 'landlord_id' => $landlord->id]);
        $request = MaintenanceRequest::create([
            'lease_id' => $lease->id,
            'user_id' => User::factory()->tenant()->create()->id,
            'title' => 'Leaking tap',
            'status' => Reported::class,
        ]);

        $action = new UpdateMaintenanceStatusAction();

        // Reported to Resolved is not allowed according to MaintenanceStatus::config()
        $this->expectException(TransitionNotFound::class);

        $action->execute($request, $landlord, Resolved::class);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_throws_exception_if_unauthorized(): void
    {
        $landlord = User::factory()->landlord()->create();
        $otherUser = User::factory()->create();
        $property = Property::factory()->create(['user_id' => $landlord->id]);
        $lease = Lease::factory()->create(['property_id' => $property->id, 'landlord_id' => $landlord->id]);
        $request = MaintenanceRequest::create([
            'lease_id' => $lease->id,
            'user_id' => User::factory()->tenant()->create()->id,
            'title' => 'Test',
            'status' => Reported::class,
        ]);

        $action = new UpdateMaintenanceStatusAction();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('You are not authorized to update this maintenance request.');

        $action->execute($request, $otherUser, InProgress::class);
    }
}

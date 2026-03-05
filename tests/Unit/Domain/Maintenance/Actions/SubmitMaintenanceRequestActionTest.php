<?php

namespace Tests\Unit\Domain\Maintenance\Actions;

use App\Domain\Identity\Models\User;
use App\Domain\Legal\Models\Lease;
use App\Domain\Legal\States\Active;
use App\Domain\Legal\States\Draft;
use App\Domain\Maintenance\Actions\SubmitMaintenanceRequestAction;
use App\Domain\Maintenance\Models\MaintenanceRequest;
use App\Domain\Maintenance\States\Reported;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubmitMaintenanceRequestActionTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_submit_a_maintenance_request_for_an_active_lease(): void
    {
        $tenant = User::factory()->tenant()->create();
        $lease = Lease::factory()->create([
            'tenant_id' => $tenant->id,
            'status' => Active::class,
        ]);

        $action = new SubmitMaintenanceRequestAction();
        $request = $action->execute(
            $lease,
            $tenant,
            'Leaking tap',
            'The kitchen tap is leaking constantly.'
        );

        $this->assertInstanceOf(MaintenanceRequest::class, $request);
        $this->assertEquals($lease->id, $request->lease_id);
        $this->assertEquals($tenant->id, $request->user_id);
        $this->assertEquals('Leaking tap', $request->title);
        $this->assertEquals('The kitchen tap is leaking constantly.', $request->description);
        $this->assertInstanceOf(Reported::class, $request->status);

        $this->assertDatabaseHas('maintenance_requests', [
            'id' => $request->id,
            'lease_id' => $lease->id,
            'user_id' => $tenant->id,
            'title' => 'Leaking tap',
            'status' => 'reported',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_cannot_submit_a_request_for_a_non_active_lease(): void
    {
        $tenant = User::factory()->tenant()->create();
        $lease = Lease::factory()->create([
            'tenant_id' => $tenant->id,
            'status' => Draft::class,
        ]);

        $action = new SubmitMaintenanceRequestAction();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Maintenance requests can only be submitted for active leases.');

        $action->execute($lease, $tenant, 'Leaking tap');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_cannot_submit_a_request_for_a_lease_the_user_does_not_belong_to(): void
    {
        $tenant = User::factory()->tenant()->create();
        $otherUser = User::factory()->tenant()->create();
        $lease = Lease::factory()->create([
            'tenant_id' => $tenant->id,
            'status' => Active::class,
        ]);

        $action = new SubmitMaintenanceRequestAction();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('You are not a tenant of this lease.');

        $action->execute($lease, $otherUser, 'Leaking tap');
    }
}

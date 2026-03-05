<?php

namespace Tests\Unit\Domain\Legal\Actions;

use App\Domain\Identity\Models\User;
use App\Domain\Legal\Actions\SignLeaseAction;
use App\Domain\Legal\Models\Lease;
use App\Domain\Legal\States\Active;
use App\Domain\Legal\States\Draft;
use App\Domain\Legal\States\WaitingLandlordSignature;
use App\Domain\Legal\States\WaitingTenantSignature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SignLeaseActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_throws_exception_if_unauthenticated(): void
    {
        $lease = Lease::factory()->create();
        $action = new SignLeaseAction();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unauthenticated.');

        $action->execute($lease);
    }

    public function test_it_throws_exception_if_unauthorized(): void
    {
        $lease = Lease::factory()->create(['status' => Draft::class]);
        $stranger = User::factory()->create();
        $this->actingAs($stranger);

        $action = new SignLeaseAction();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unauthorized or invalid lease state for signing.');

        $action->execute($lease);
    }

    public function test_landlord_can_sign(): void
    {
        $landlord = User::factory()->landlord()->create();
        $lease = Lease::factory()->create([
            'landlord_id' => $landlord->id,
            'status' => WaitingLandlordSignature::class
        ]);
        $this->actingAs($landlord);

        $action = new SignLeaseAction();
        $result = $action->execute($lease);

        $this->assertInstanceOf(WaitingTenantSignature::class, $result->status);
    }

    public function test_tenant_can_sign(): void
    {
        $tenant = User::factory()->tenant()->create();
        $lease = Lease::factory()->create([
            'tenant_id' => $tenant->id,
            'status' => WaitingTenantSignature::class
        ]);
        $this->actingAs($tenant);

        $action = new SignLeaseAction();
        $result = $action->execute($lease);

        $this->assertInstanceOf(Active::class, $result->status);
    }
}

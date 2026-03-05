<?php

namespace Tests\Feature;

use App\Domain\Financial\Models\Payment;
use App\Domain\Financial\States\Pending as PaymentPending;
use App\Domain\Maintenance\Models\MaintenanceRequest;
use App\Domain\Maintenance\States\Reported;
use App\Domain\Legal\Models\Lease;
use App\Domain\Identity\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DomainScaffoldingTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_create_a_payment_with_initial_state(): void
    {
        $lease = Lease::factory()->create();
        
        $payment = Payment::create([
            'lease_id' => $lease->id,
            'amount' => 1500.00,
            'type' => 'first_month_rent',
            'status' => PaymentPending::class,
        ]);

        $this->assertDatabaseHas('payments', [
            'lease_id' => $lease->id,
            'amount' => 1500.00,
            'status' => 'pending',
        ]);
        
        $this->assertInstanceOf(PaymentPending::class, $payment->status);
        $this->assertEquals($lease->id, $payment->lease->id);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_create_a_maintenance_request_with_initial_state(): void
    {
        $lease = Lease::factory()->create();
        $tenant = $lease->tenant;

        $request = MaintenanceRequest::create([
            'lease_id' => $lease->id,
            'user_id' => $tenant->id,
            'title' => 'Broken Faucet',
            'description' => 'The kitchen faucet is leaking.',
            'status' => Reported::class,
        ]);

        $this->assertDatabaseHas('maintenance_requests', [
            'lease_id' => $lease->id,
            'user_id' => $tenant->id,
            'title' => 'Broken Faucet',
            'status' => 'reported',
        ]);

        $this->assertInstanceOf(Reported::class, $request->status);
        $this->assertEquals($tenant->id, $request->user->id);
    }
}

<?php

namespace Tests\Unit\Domain\Legal\Actions;

use App\Domain\Identity\Models\User;
use App\Domain\Legal\Actions\SignLeaseAction;
use App\Domain\Legal\Models\Lease;
use App\Domain\Legal\States\Draft;
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
}

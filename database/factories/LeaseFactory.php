<?php

namespace Database\Factories;

use App\Domain\Identity\Models\User;
use App\Domain\Legal\Models\Lease;
use App\Domain\Property\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domain\Legal\Models\Lease>
 */
class LeaseFactory extends Factory
{
    protected $model = Lease::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'property_id' => Property::factory(),
            'landlord_id' => User::factory(),
            'tenant_id' => User::factory(),
            'start_date' => now()->toDateString(),
            'end_date' => now()->addYear()->toDateString(),
            'rent_amount' => 2000,
            'status' => 'active',
        ];
    }
}

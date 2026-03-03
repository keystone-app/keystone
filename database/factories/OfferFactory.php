<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Property;
use App\Models\Visit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Offer>
 */
class OfferFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'property_id' => Property::factory(),
            'visit_id' => Visit::factory(),
            'amount' => $this->faker->numberBetween(1000, 5000),
            'terms' => 'Standard legal terms as per Keystone framework.',
            'status' => 'pending',
        ];
    }
}

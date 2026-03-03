<?php

namespace Database\Factories;

use App\Domain\Identity\Models\User;
use App\Domain\Negotiation\Models\Offer;
use App\Domain\Property\Models\Property;
use App\Domain\Scheduling\Models\Visit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domain\Negotiation\Models\Offer>
 */
class OfferFactory extends Factory
{
    protected $model = Offer::class;
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

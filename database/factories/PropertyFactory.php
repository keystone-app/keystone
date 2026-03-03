<?php

namespace Database\Factories;

use App\Domain\Identity\Models\User;
use App\Domain\Property\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domain\Property\Models\Property>
 */
class PropertyFactory extends Factory
{
    protected $model = Property::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->company . ' Apartment',
            'address' => $this->faker->address,
            'description' => $this->faker->paragraph,
            'status' => 'available',
        ];
    }
}

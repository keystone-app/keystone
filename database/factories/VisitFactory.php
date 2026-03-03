<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Property;
use App\Models\Document;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Visit>
 */
class VisitFactory extends Factory
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
            'document_id' => Document::factory(),
            'visit_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'status' => 'pending',
        ];
    }
}

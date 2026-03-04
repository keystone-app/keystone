<?php

namespace Database\Factories;

use App\Domain\Identity\Models\User;
use App\Domain\Legal\Models\Document;
use App\Domain\Property\Models\Property;
use App\Domain\Scheduling\Models\Visit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domain\Scheduling\Models\Visit>
 */
class VisitFactory extends Factory
{
    protected $model = Visit::class;

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

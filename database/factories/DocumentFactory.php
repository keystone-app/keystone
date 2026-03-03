<?php

namespace Database\Factories;

use App\Domain\Identity\Models\User;
use App\Domain\Legal\Models\Document;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domain\Legal\Models\Document>
 */
class DocumentFactory extends Factory
{
    protected $model = Document::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => 'identity_card.png',
            'path' => 'identity_docs/mock_id.png',
            'type' => 'identity_doc',
        ];
    }
}

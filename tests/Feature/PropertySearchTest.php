<?php

namespace Tests\Feature;

use App\Domain\Property\Models\Property;
use App\Domain\Property\States\Available;
use App\Domain\Property\States\Rented;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertySearchTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_filter_properties_by_price_range(): void
    {
        Property::factory()->create(['price' => 1000]);
        Property::factory()->create(['price' => 2000]);
        Property::factory()->create(['price' => 3000]);

        $response = $this->getJson('/properties?min_price=1500&max_price=2500');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonPath('0.price', '2000.00');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_filter_properties_by_type(): void
    {
        Property::factory()->create(['type' => 'Apartment']);
        Property::factory()->create(['type' => 'House']);

        $response = $this->getJson('/properties?type=House');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonPath('0.type', 'House');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_filter_properties_by_status(): void
    {
        Property::factory()->create(['status' => Available::class]);
        Property::factory()->create(['status' => Rented::class]);

        $response = $this->getJson('/properties?status=available');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        // Spatie states might serialize differently, checking for presence of 'available'
        $response->assertJsonFragment(['status' => 'available']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_combine_multiple_filters(): void
    {
        Property::factory()->create(['price' => 1000, 'type' => 'Apartment', 'status' => Available::class]);
        Property::factory()->create(['price' => 2000, 'type' => 'Apartment', 'status' => Available::class]);
        Property::factory()->create(['price' => 2000, 'type' => 'House', 'status' => Available::class]);
        Property::factory()->create(['price' => 2000, 'type' => 'Apartment', 'status' => Rented::class]);

        $response = $this->getJson('/properties?min_price=1500&type=Apartment&status=available');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonPath('0.price', '2000.00');
        $response->assertJsonPath('0.type', 'Apartment');
        $response->assertJsonFragment(['status' => 'available']);
    }
}

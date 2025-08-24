<?php

namespace Database\Factories;

use App\Models\FacilityIva;
use Illuminate\Database\Eloquent\Factories\Factory;

class FacilityIvaFactory extends Factory
{
    protected $model = FacilityIva::class;

    public function definition(): array
    {
        $locations = FacilityIva::AVAILABLE_LOCATIONS;
        $selectedLocation = $this->faker->randomElement($locations);
        
        // Koordinat sample untuk Maluku (area umum)
        $coordinates = $this->generateMalukuCoordinates();
        
        return [
            'code' => '108' . str_pad($this->faker->unique()->numberBetween(1000, 9999), 4, '0', STR_PAD_LEFT),
            'name' => strtoupper($this->faker->words(2, true)),
            'location' => $selectedLocation,
            'address' => $this->faker->address(),
            'latitude' => $coordinates['lat'],
            'longitude' => $coordinates['lng'],
            'phone' => $this->faker->optional(0.7)->phoneNumber(),
            'iva_training_years' => $this->faker->optional(0.4)->randomElements(
                [2019, 2020, 2021, 2022, 2023],
                $this->faker->numberBetween(1, 3)
            ),
            'is_active' => $this->faker->boolean(85), // 85% chance aktif
            'notes' => $this->faker->optional(0.3)->sentence(),
        ];
    }

    /**
     * Generate coordinates within Maluku province bounds
     */
    private function generateMalukuCoordinates(): array
    {
        // Bounds untuk Provinsi Maluku
        $minLat = -8.5;
        $maxLat = -3.0;
        $minLng = 125.0;
        $maxLng = 135.0;
        
        return [
            'lat' => $this->faker->randomFloat(6, $minLat, $maxLat),
            'lng' => $this->faker->randomFloat(6, $minLng, $maxLng)
        ];
    }

    /**
     * Create facility with training
     */
    public function withTraining(): static
    {
        return $this->state(fn (array $attributes) => [
            'iva_training_years' => $this->faker->randomElements(
                [2019, 2020, 2021, 2022, 2023],
                $this->faker->numberBetween(1, 3)
            ),
        ]);
    }

    /**
     * Create active facility
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Create inactive facility
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Create facility without coordinates
     */
    public function withoutCoordinates(): static
    {
        return $this->state(fn (array $attributes) => [
            'latitude' => null,
            'longitude' => null,
        ]);
    }

    /**
     * Create facility for specific location
     */
    public function forLocation(string $location): static
    {
        return $this->state(fn (array $attributes) => [
            'location' => $location,
        ]);
    }
}
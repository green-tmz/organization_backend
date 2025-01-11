<?php

namespace Database\Factories;

use App\Modules\Api\Building\Models\Building;
use Clickbar\Magellan\Data\Geometries\Point;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Building>
 */
class BuildingFactory extends Factory
{
    protected $model = Building::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'city' => $this->faker->city,
            'street' => $this->faker->streetName,
            'office' => $this->faker->numberBetween(1, 100),
            'location' => Point::makeGeodetic(26.193056, -80.161111),
        ];
    }
}

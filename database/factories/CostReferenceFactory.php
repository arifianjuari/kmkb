<?php

namespace Database\Factories;

use App\Models\CostReference;
use Illuminate\Database\Eloquent\Factories\Factory;

class CostReferenceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CostReference::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'service_code' => $this->faker->unique()->regexify('[A-Z]{3}[0-9]{4}'),
            'service_description' => $this->faker->sentence(),
            'standard_cost' => $this->faker->randomFloat(2, 10000, 10000000),
            'unit' => $this->faker->randomElement(['Procedure', 'Service', 'Item']),
            'source' => $this->faker->randomElement(['INA-CBG', 'Hospital Tariff', 'Regional Tariff']),
            'hospital_id' => null,
        ];
    }
}

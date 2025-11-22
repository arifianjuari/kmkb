<?php

namespace Database\Factories;

use App\Models\ClinicalPathway;
use App\Models\Hospital;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClinicalPathwayFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ClinicalPathway::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'diagnosis_code' => $this->faker->regexify('[A-Z]{1}[0-9]{2}'),
            'version' => $this->faker->randomNumber(2),
            'effective_date' => $this->faker->date(),
            'status' => $this->faker->randomElement(['active', 'inactive', 'draft']),
            'created_by' => 1,
            'hospital_id' => Hospital::factory(),
        ];
    }
}

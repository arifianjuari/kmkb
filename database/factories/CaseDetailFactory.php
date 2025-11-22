<?php

namespace Database\Factories;

use App\Models\CaseDetail;
use App\Models\PatientCase;
use App\Models\Hospital;
use Illuminate\Database\Eloquent\Factories\Factory;

class CaseDetailFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CaseDetail::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'patient_case_id' => PatientCase::factory(),
            'pathway_step_id' => null,
            'service_item' => $this->faker->sentence(),
            'service_code' => $this->faker->unique()->regexify('[A-Z]{3}[0-9]{3}'),
            'status' => $this->faker->randomElement(['pending', 'completed', 'skipped']),
            'performed' => $this->faker->boolean(),
            'quantity' => $this->faker->numberBetween(1, 10),
            'actual_cost' => $this->faker->randomFloat(2, 10000, 500000),
            'service_date' => $this->faker->date(),
            'hospital_id' => Hospital::factory(),
        ];
    }
}

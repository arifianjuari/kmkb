<?php

namespace Database\Factories;

use App\Models\PatientCase;
use App\Models\ClinicalPathway;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientCaseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PatientCase::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'patient_id' => $this->faker->unique()->numerify('PAT###'),
            'medical_record_number' => $this->faker->unique()->regexify('[A-Z]{2}[0-9]{6}'),
            'clinical_pathway_id' => ClinicalPathway::factory(),
            'admission_date' => $this->faker->date(),
            'discharge_date' => $this->faker->date(),
            'primary_diagnosis' => $this->faker->sentence(),
            'ina_cbg_code' => $this->faker->unique()->regexify('CBG[0-9]{3}'),
            'actual_total_cost' => $this->faker->randomFloat(2, 100000, 5000000),
            'ina_cbg_tariff' => $this->faker->randomFloat(2, 100000, 5000000),
            'compliance_percentage' => $this->faker->randomFloat(2, 0, 100),
            'cost_variance' => $this->faker->randomFloat(2, -100000, 100000),
            'input_by' => $this->faker->name(),
            'input_date' => $this->faker->dateTime(),
        ];
    }
}

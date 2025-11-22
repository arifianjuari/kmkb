<?php

namespace Database\Factories;

use App\Models\Hospital;
use Illuminate\Database\Eloquent\Factories\Factory;

class HospitalFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Hospital::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->company,
            'code' => $this->faker->unique()->lexify('HOSP???'),
            'logo_path' => $this->faker->imageUrl(200, 200, 'business'),
            'theme_color' => $this->faker->hexColor,
            'address' => $this->faker->address,
            'contact' => $this->faker->phoneNumber,
            'is_active' => true,
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Hospital;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'), // Default password
            'remember_token' => Str::random(10),
            'role' => User::ROLE_HOSPITAL_ADMIN, // Default role
            'department' => $this->faker->word(),
            'hospital_id' => Hospital::factory(),
        ];
    }

    /**
     * Indicate that the user is a superadmin.
     */
    public function superadmin()
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => User::ROLE_SUPERADMIN,
                'hospital_id' => null,
                'department' => 'Global Administration',
            ];
        });
    }

    /**
     * Indicate that the user is a hospital admin.
     */
    public function hospitalAdmin()
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => User::ROLE_HOSPITAL_ADMIN,
                'department' => 'Administration',
            ];
        });
    }

    /**
     * Indicate that the user is an observer (read-only).
     */
    public function observer()
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => User::ROLE_OBSERVER,
                'department' => 'Monitoring & Audit',
            ];
        });
    }

    /**
     * Indicate that the user is a management auditor (read-only).
     */
    public function managementAuditor()
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => User::ROLE_MANAGEMENT_AUDITOR,
                'department' => 'Audit',
            ];
        });
    }
}

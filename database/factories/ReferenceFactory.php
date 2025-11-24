<?php

namespace Database\Factories;

use App\Models\Hospital;
use App\Models\Reference;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\Reference>
 */
class ReferenceFactory extends Factory
{
    protected $model = Reference::class;

    public function definition(): array
    {
        $title = $this->faker->unique()->sentence(4);
        $status = $this->faker->randomElement([
            Reference::STATUS_DRAFT,
            Reference::STATUS_PUBLISHED,
            Reference::STATUS_ARCHIVED,
        ]);

        return [
            'hospital_id' => Hospital::inRandomOrder()->value('id') ?? Hospital::factory(),
            'author_id' => User::inRandomOrder()->value('id') ?? User::factory(),
            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::random(5),
            'content' => $this->faker->paragraphs(4, true),
            'status' => $status,
            'is_pinned' => $this->faker->boolean(15),
            'published_at' => $status === Reference::STATUS_PUBLISHED ? now()->subDays(rand(0, 30)) : null,
            'view_count' => $this->faker->numberBetween(0, 250),
        ];
    }
}


<?php

namespace Database\Seeders;

use App\Models\Hospital;
use App\Models\Reference;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReferencesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Reference::count() > 0) {
            return;
        }

        $hospitals = Hospital::with('users')->get();

        foreach ($hospitals as $hospital) {
            /** @var \Illuminate\Support\Collection<int, User> $authors */
            $authors = $hospital->users;

            if ($authors->isEmpty()) {
                continue;
            }

            Reference::factory()
                ->count(3)
                ->state(function () use ($hospital, $authors) {
                    return [
                        'hospital_id' => $hospital->id,
                        'author_id' => $authors->random()->id,
                    ];
                })
                ->create();
        }
    }
}


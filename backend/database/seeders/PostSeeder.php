<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use Faker\Factory as Faker;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // create 10 fake posts
        for ($i = 0; $i < 10; $i++) {
            Post::create([
                'user_id'     => rand(1, 10), // assuming you already seeded 10 users
                'title'       => $faker->sentence,
                'description' => $faker->paragraph,
            ]);
        }
    }
}

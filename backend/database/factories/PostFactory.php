<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
public function definition(): array
{
    return [
        'title' => $this->faker->sentence,
        'content' => $this->faker->paragraph(5),
        'user_id' => \App\Models\User::inRandomOrder()->first()->id ?? 1,
    ];
}

}

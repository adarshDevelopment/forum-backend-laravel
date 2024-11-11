<?php

namespace Database\Factories;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'slug' => $this->faker->slug(),
            'content' => $this->faker->paragraph(1),
            'picture_exists' => random_int(0, 1),
            // 'likes' => random_int(-10, 50),
            'user_id' => random_int(1, 10),        // will automatically create a new user
            'tag_id' => random_int(1, 10),


        ];
    }
}

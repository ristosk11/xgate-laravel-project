<?php

namespace Database\Factories;

use App\Domain\Content\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'content' => fake()->optional(0.9)->paragraphs(rand(1, 3), true),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Domain\Content\Models\Post;
use App\Domain\Engagement\Models\Comment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'post_id' => Post::factory(),
            'parent_comment_id' => null,
            'content' => fake()->sentences(rand(1, 3), true),
        ];
    }
}

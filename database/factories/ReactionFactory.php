<?php

namespace Database\Factories;

use App\Domain\Content\Models\Post;
use App\Domain\Engagement\Enums\ReactionType;
use App\Domain\Engagement\Models\Reaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReactionFactory extends Factory
{
    protected $model = Reaction::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'reactable_type' => Post::class,
            'reactable_id' => Post::factory(),
            'type' => fake()->randomElement(ReactionType::cases()),
        ];
    }
}

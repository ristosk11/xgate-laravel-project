<?php

namespace Database\Factories;

use App\Domain\IdentityAndAccess\Models\Profile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    protected $model = Profile::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'bio' => fake()->optional()->paragraph(),
            'avatar_path' => fake()->optional()->imageUrl(300, 300, 'people'),
            'cover_image_path' => fake()->optional()->imageUrl(1200, 400, 'nature'),
            'location' => fake()->optional()->city().', '.fake()->country(),
            'website' => fake()->optional()->url(),
        ];
    }
}

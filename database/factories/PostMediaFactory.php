<?php

namespace Database\Factories;

use App\Domain\Content\Enums\MediaType;
use App\Domain\Content\Models\Post;
use App\Domain\Content\Models\PostMedia;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostMediaFactory extends Factory
{
    protected $model = PostMedia::class;

    public function definition(): array
    {
        $type = fake()->randomElement([MediaType::Image, MediaType::Video]);

        return [
            'post_id' => Post::factory(),
            'file_path' => $type === MediaType::Image
                ? 'posts/'.fake()->uuid().'.jpg'
                : 'posts/'.fake()->uuid().'.mp4',
            'type' => $type,
            'display_order' => fake()->numberBetween(0, 4),
            'alt_text' => $type === MediaType::Image ? fake()->optional()->sentence() : null,
        ];
    }
}

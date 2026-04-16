<?php

namespace Database\Seeders;

use App\Domain\Content\Models\Post;
use App\Domain\IdentityAndAccess\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ExampleUsersSeeder extends Seeder
{
    public function run(): void
    {
        $examples = [
            [
                'name' => 'Design',
                'email' => 'design@example.com',
                'posts' => [
                    'Design is not just what it looks like, but how it works.',
                    'Small UI tweaks can make a product feel fast.',
                ],
            ],
            [
                'name' => 'Frontend',
                'email' => 'frontend@example.com',
                'posts' => [
                    'Progressive enhancement first, JavaScript second.',
                    'Ship small, measure, iterate.',
                ],
            ],
            [
                'name' => 'Laravel',
                'email' => 'laravel@example.com',
                'posts' => [
                    'Keep controllers thin; push work into actions and models.',
                    'Prefer deterministic fixtures in seeds to keep tests stable.',
                ],
            ],
        ];

        foreach ($examples as $example) {
            $user = User::query()->updateOrCreate(
                ['email' => $example['email']],
                [
                    'name' => $example['name'],
                    'password' => Hash::make('password'),
                ]
            );

            Profile::query()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'bio' => null,
                    'avatar_path' => null,
                    'cover_image_path' => null,
                    'location' => null,
                    'website' => null,
                ]
            );

            foreach ($example['posts'] as $content) {
                Post::query()->firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'content' => $content,
                    ],
                    []
                );
            }
        }
    }
}

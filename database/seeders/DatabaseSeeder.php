<?php

namespace Database\Seeders;

use App\Domain\Content\Enums\MediaType;
use App\Domain\Content\Models\Post;
use App\Domain\Content\Models\PostMedia;
use App\Domain\IdentityAndAccess\Models\Profile;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $lorem = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Curabitur pretium tincidunt lacus. Nulla gravida orci a odio. Nullam varius, turpis et commodo pharetra. Est eros bibendum elit, nec luctus magna felis sollicitudin mauris. Integer in mauris eu nibh euismod gravida. Duis ac tellus et risus vulputate vehicula.";

        $users = [
            ['name' => 'Design', 'email' => 'design@example.test', 'avatar' => 'avatars/design.jpg'],
            ['name' => 'Frontend', 'email' => 'frontend@example.test', 'avatar' => 'avatars/frontend.jpg'],
            ['name' => 'Laravel', 'email' => 'laravel@example.test', 'avatar' => 'avatars/laravel.jpg'],
        ];

        $seedImages = ['posts/seed1.jpg', 'posts/seed2.jpg', 'posts/seed3.jpg'];
        $imageIndex = 0;

        foreach ($users as $data) {
            $user = User::query()->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('password'),
            ]);

            Profile::query()->create([
                'user_id' => $user->id,
                'avatar_path' => $data['avatar'],
            ]);

            Post::query()->create([
                'user_id' => $user->id,
                'content' => $lorem,
            ]);

            $post2 = Post::query()->create([
                'user_id' => $user->id,
                'content' => $lorem,
            ]);

            PostMedia::query()->create([
                'post_id' => $post2->id,
                'file_path' => $seedImages[$imageIndex % count($seedImages)],
                'type' => MediaType::Image,
                'display_order' => 0,
            ]);

            $imageIndex++;
        }

        $this->call(TestUserSeeder::class);
    }
}

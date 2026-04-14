<?php

namespace Database\Seeders;

use App\Domain\Content\Enums\MediaType;
use App\Domain\Content\Models\Post;
use App\Domain\Content\Models\PostMedia;
use App\Domain\Engagement\Enums\ReactionType;
use App\Domain\Engagement\Models\Comment;
use App\Domain\Engagement\Models\Reaction;
use App\Domain\IdentityAndAccess\Models\Follow;
use App\Domain\IdentityAndAccess\Models\Profile;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = User::factory(12)->create();
        // Seed test users from dedicated seeder
        $this->call(TestUserSeeder::class);

        $users->each(function (User $user): void {
            Profile::factory()->create([
                'user_id' => $user->id,
            ]);
        });

        $posts = collect();

        $users->each(function (User $user) use (&$posts): void {
            $userPosts = Post::factory()
                ->count(rand(2, 4))
                ->create([
                    'user_id' => $user->id,
                ]);

            $posts = $posts->merge($userPosts);
        });

        $posts->each(function (Post $post): void {
            $mediaCount = fake()->randomElement([0, 1, 1, 2, 2, 3]);

            for ($displayOrder = 0; $displayOrder < $mediaCount; $displayOrder++) {
                $mediaType = fake()->boolean(75) ? MediaType::Image : MediaType::Video;

                PostMedia::factory()->create([
                    'post_id' => $post->id,
                    'type' => $mediaType,
                    'display_order' => $displayOrder,
                    'file_path' => $mediaType === MediaType::Image
                        ? 'posts/'.fake()->uuid().'.jpg'
                        : 'posts/'.fake()->uuid().'.mp4',
                ]);
            }
        });

        $users->each(function (User $follower) use ($users): void {
            $targets = $users
                ->where('id', '!=', $follower->id)
                ->shuffle()
                ->take(rand(3, 5));

            foreach ($targets as $following) {
                Follow::query()->firstOrCreate([
                    'follower_id' => $follower->id,
                    'following_id' => $following->id,
                ]);
            }
        });

        $allComments = collect();

        $posts->each(function (Post $post) use ($users, &$allComments): void {
            $parents = collect();

            foreach (range(1, rand(1, 5)) as $index) {
                $author = $users->random();

                $parent = Comment::factory()->create([
                    'user_id' => $author->id,
                    'post_id' => $post->id,
                    'parent_comment_id' => null,
                ]);

                $parents->push($parent);
                $allComments->push($parent);
            }

            $parents->each(function (Comment $parent) use ($users, &$allComments, $post): void {
                foreach (range(1, rand(0, 2)) as $index) {
                    $author = $users->random();

                    $reply = Comment::factory()->create([
                        'user_id' => $author->id,
                        'post_id' => $post->id,
                        'parent_comment_id' => $parent->id,
                    ]);

                    $allComments->push($reply);
                }
            });
        });

        $posts->each(function (Post $post) use ($users): void {
            $reactors = $users->shuffle()->take(rand(1, min(8, $users->count())));

            foreach ($reactors as $reactor) {
                Reaction::query()->create([
                    'user_id' => $reactor->id,
                    'reactable_type' => Post::class,
                    'reactable_id' => $post->id,
                    'type' => fake()->randomElement(ReactionType::cases()),
                ]);
            }
        });

        $allComments->each(function (Comment $comment) use ($users): void {
            $reactors = $users->shuffle()->take(rand(0, min(4, $users->count())));

            foreach ($reactors as $reactor) {
                Reaction::query()->create([
                    'user_id' => $reactor->id,
                    'reactable_type' => Comment::class,
                    'reactable_id' => $comment->id,
                    'type' => fake()->randomElement(ReactionType::cases()),
                ]);
            }
        });
    }
}

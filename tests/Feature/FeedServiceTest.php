<?php

namespace Tests\Feature;

use App\Domain\Content\Models\Post;
use App\Domain\Content\Services\FeedService;
use App\Domain\Engagement\Actions\ToggleReactionAction;
use App\Domain\Engagement\Enums\ReactionType;
use App\Domain\IdentityAndAccess\Models\Follow;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeedServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_feed_returns_only_followed_users_posts_when_follows_exist(): void
    {
        $viewer = User::factory()->create();
        $followed = User::factory()->create();
        $other = User::factory()->create();

        Follow::factory()->create([
            'follower_id' => $viewer->id,
            'following_id' => $followed->id,
        ]);

        $followedPost = Post::factory()->create(['user_id' => $followed->id]);
        $otherPost = Post::factory()->create(['user_id' => $other->id]);

        $feed = app(FeedService::class)->getFeed($viewer, 15);

        $ids = collect($feed->items())->pluck('id');

        $this->assertTrue($ids->contains($followedPost->id));
        $this->assertFalse($ids->contains($otherPost->id));
    }

    public function test_feed_includes_grouped_reaction_summary_and_current_user_reaction(): void
    {
        $viewer = User::factory()->create();
        $followed = User::factory()->create();
        $otherReactor = User::factory()->create();

        Follow::factory()->create([
            'follower_id' => $viewer->id,
            'following_id' => $followed->id,
        ]);

        $post = Post::factory()->create(['user_id' => $followed->id]);

        $toggle = app(ToggleReactionAction::class);
        $toggle->execute($viewer, $post, ReactionType::Love);
        $toggle->execute($otherReactor, $post, ReactionType::Like);

        $feed = app(FeedService::class)->getFeed($viewer, 15);
        $firstPost = collect($feed->items())->first();

        $this->assertNotNull($firstPost);
        $this->assertSame(1, (int) ($firstPost->reaction_summary['love'] ?? 0));
        $this->assertSame(1, (int) ($firstPost->reaction_summary['like'] ?? 0));
        $this->assertSame('love', $firstPost->current_user_reaction);
    }
}

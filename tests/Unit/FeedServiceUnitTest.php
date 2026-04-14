<?php

namespace Tests\Unit;

use App\Domain\Content\Models\Post;
use App\Domain\Content\Services\FeedService;
use App\Domain\Engagement\Actions\ToggleReactionAction;
use App\Domain\Engagement\Enums\ReactionType;
use App\Domain\IdentityAndAccess\Models\Follow;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeedServiceUnitTest extends TestCase
{
    use RefreshDatabase;

    public function test_feed_service_includes_grouped_reaction_data_for_followed_posts(): void
    {
        $viewer = User::factory()->create();
        $followed = User::factory()->create();
        $reactor = User::factory()->create();

        Follow::factory()->create([
            'follower_id' => $viewer->id,
            'following_id' => $followed->id,
        ]);

        $post = Post::factory()->create(['user_id' => $followed->id]);

        $toggle = app(ToggleReactionAction::class);
        $toggle->execute($viewer, $post, ReactionType::Wow);
        $toggle->execute($reactor, $post, ReactionType::Like);

        $feed = app(FeedService::class)->getFeed($viewer, 15);
        $item = collect($feed->items())->first();

        $this->assertNotNull($item);
        $this->assertSame(1, (int) ($item->reaction_summary['wow'] ?? 0));
        $this->assertSame(1, (int) ($item->reaction_summary['like'] ?? 0));
        $this->assertSame('wow', $item->current_user_reaction);
    }
}

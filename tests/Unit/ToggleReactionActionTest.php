<?php

namespace Tests\Unit;

use App\Domain\Content\Models\Post;
use App\Domain\Engagement\Actions\ToggleReactionAction;
use App\Domain\Engagement\Enums\ReactionType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ToggleReactionActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_toggle_reaction_action_supports_all_paths(): void
    {
        $author = User::factory()->create();
        $reactor = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $author->id]);

        $action = app(ToggleReactionAction::class);

        $created = $action->execute($reactor, $post, ReactionType::Like);
        $this->assertSame(1, (int) ($created['like'] ?? 0));

        $updated = $action->execute($reactor, $post, ReactionType::Wow);
        $this->assertSame(1, (int) ($updated['wow'] ?? 0));

        $deleted = $action->execute($reactor, $post, ReactionType::Wow);
        $this->assertSame(0, (int) ($deleted['wow'] ?? 0));
    }
}

<?php

namespace Tests\Feature;

use App\Domain\Content\Models\Post;
use App\Domain\Engagement\Actions\CreateCommentAction;
use App\Domain\Engagement\Actions\ToggleReactionAction;
use App\Domain\Engagement\DTOs\CreateCommentDTO;
use App\Domain\Engagement\Enums\ReactionType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReactionToggleTest extends TestCase
{
    use RefreshDatabase;

    public function test_reaction_toggle_create_update_delete_paths(): void
    {
        $author = User::factory()->create();
        $reactor = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $author->id]);

        $action = app(ToggleReactionAction::class);

        $counts = $action->execute($reactor, $post, ReactionType::Like);
        $this->assertSame(1, (int) ($counts['like'] ?? 0));

        $counts = $action->execute($reactor, $post, ReactionType::Love);
        $this->assertSame(0, (int) ($counts['like'] ?? 0));
        $this->assertSame(1, (int) ($counts['love'] ?? 0));

        $counts = $action->execute($reactor, $post, ReactionType::Love);
        $this->assertSame(0, (int) ($counts['love'] ?? 0));
    }

    public function test_user_can_react_to_comment_polymorphically(): void
    {
        $author = User::factory()->create();
        $reactor = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $author->id]);

        $comment = app(CreateCommentAction::class)->execute(
            $author,
            $post,
            new CreateCommentDTO(content: 'A comment', parentCommentId: null)
        );

        $counts = app(ToggleReactionAction::class)->execute($reactor, $comment, ReactionType::Laugh);

        $this->assertSame(1, (int) ($counts['laugh'] ?? 0));
        $this->assertDatabaseHas('reactions', [
            'user_id' => $reactor->id,
            'reactable_type' => $comment::class,
            'reactable_id' => $comment->id,
            'type' => ReactionType::Laugh->value,
        ]);
    }
}

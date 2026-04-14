<?php

namespace Tests\Unit;

use App\Domain\Content\Actions\DeletePostAction;
use App\Domain\Content\Models\Post;
use App\Domain\Engagement\Actions\CreateCommentAction;
use App\Domain\Engagement\Actions\DeleteCommentAction;
use App\Domain\Engagement\Actions\ToggleReactionAction;
use App\Domain\Engagement\DTOs\CreateCommentDTO;
use App\Domain\Engagement\Enums\ReactionType;
use App\Domain\Engagement\Models\Comment;
use App\Domain\Engagement\Models\Reaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteCommentActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_delete_comment_action_removes_parent_replies_and_reactions(): void
    {
        $author = User::factory()->create();
        $reactor = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $author->id]);

        $createCommentAction = app(CreateCommentAction::class);

        $parent = $createCommentAction->execute(
            $author,
            $post,
            new CreateCommentDTO(content: 'Parent', parentCommentId: null)
        );

        $reply = $createCommentAction->execute(
            $author,
            $post,
            new CreateCommentDTO(content: 'Reply', parentCommentId: $parent->id)
        );

        app(ToggleReactionAction::class)->execute($reactor, $parent, ReactionType::Like);
        app(ToggleReactionAction::class)->execute($reactor, $reply, ReactionType::Love);

        app(DeleteCommentAction::class)->execute($parent);

        $this->assertDatabaseMissing('comments', ['id' => $parent->id]);
        $this->assertDatabaseMissing('comments', ['id' => $reply->id]);
        $this->assertSame(0, Comment::query()->count());
        $this->assertSame(0, Reaction::query()->count());
    }

    public function test_delete_post_action_removes_post_and_comment_reactions(): void
    {
        $author = User::factory()->create();
        $reactor = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $author->id]);

        $createCommentAction = app(CreateCommentAction::class);

        $comment = $createCommentAction->execute(
            $author,
            $post,
            new CreateCommentDTO(content: 'Parent', parentCommentId: null)
        );

        app(ToggleReactionAction::class)->execute($reactor, $post, ReactionType::Like);
        app(ToggleReactionAction::class)->execute($reactor, $comment, ReactionType::Love);

        app(DeletePostAction::class)->execute($post);

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
        $this->assertSame(0, Reaction::query()->count());
    }
}

<?php

namespace Tests\Feature;

use App\Domain\Content\Models\Post;
use App\Domain\Engagement\Actions\CreateCommentAction;
use App\Domain\Engagement\DTOs\CreateCommentDTO;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NestedCommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_replies_to_replies_are_flattened_under_parent(): void
    {
        $author = User::factory()->create();
        $replier = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $author->id]);

        $action = app(CreateCommentAction::class);

        $parent = $action->execute(
            $author,
            $post,
            new CreateCommentDTO(content: 'Parent', parentCommentId: null)
        );

        $reply = $action->execute(
            $replier,
            $post,
            new CreateCommentDTO(content: 'Reply', parentCommentId: $parent->id)
        );

        $replyToReply = $action->execute(
            $author,
            $post,
            new CreateCommentDTO(content: 'Reply to reply', parentCommentId: $reply->id)
        );

        $this->assertSame($parent->id, $reply->parent_comment_id);
        $this->assertSame($parent->id, $replyToReply->parent_comment_id);
    }
}

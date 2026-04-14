<?php

namespace Tests\Feature;

use App\Domain\Content\Models\Post;
use App\Domain\Engagement\Actions\CreateCommentAction;
use App\Domain\Engagement\DTOs\CreateCommentDTO;
use App\Domain\Engagement\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class CommentMentionReplyTest extends TestCase
{
    use RefreshDatabase;

    public function test_reply_to_reply_creates_comment_under_parent_with_mention(): void
    {
        $alice = User::factory()->create(['name' => 'Alice']);
        $bob = User::factory()->create(['name' => 'Bob']);
        $charlie = User::factory()->create(['name' => 'Charlie']);
        $post = Post::factory()->create(['user_id' => $alice->id]);

        $action = app(CreateCommentAction::class);

        // Given: a top-level comment and a reply
        $parent = $action->execute(
            $alice,
            $post,
            new CreateCommentDTO(content: 'Top-level comment', parentCommentId: null)
        );
        $reply = $action->execute(
            $bob,
            $post,
            new CreateCommentDTO(content: 'Reply from Bob', parentCommentId: $parent->id)
        );

        // When: Charlie replies to Bob's reply with a @mention
        $mentionReply = $action->execute(
            $charlie,
            $post,
            new CreateCommentDTO(content: '@Bob What do you think?', parentCommentId: $reply->id)
        );

        // Then: flattened under parent, content preserved, attributed to Charlie
        $this->assertSame($parent->id, $mentionReply->parent_comment_id);
        $this->assertSame('@Bob What do you think?', $mentionReply->content);
        $this->assertSame($charlie->id, $mentionReply->user_id);
        $this->assertCount(2, Comment::query()->where('parent_comment_id', $parent->id)->get());
    }

    public function test_reply_to_reply_targets_original_parent_not_the_reply(): void
    {
        $alice = User::factory()->create(['name' => 'Alice']);
        $bob = User::factory()->create(['name' => 'Bob']);
        $post = Post::factory()->create(['user_id' => $alice->id]);

        $action = app(CreateCommentAction::class);

        // Given: parent comment and a reply
        $parent = $action->execute(
            $alice,
            $post,
            new CreateCommentDTO(content: 'Parent comment', parentCommentId: null)
        );
        $reply = $action->execute(
            $bob,
            $post,
            new CreateCommentDTO(content: 'First reply', parentCommentId: $parent->id)
        );

        // When: replying to the reply
        $nestedReply = $action->execute(
            $alice,
            $post,
            new CreateCommentDTO(content: '@Bob Agreed!', parentCommentId: $reply->id)
        );

        // Then: flattened to parent, not nested under reply
        $this->assertNotSame($reply->id, $nestedReply->parent_comment_id);
        $this->assertSame($parent->id, $nestedReply->parent_comment_id);
    }

    public function test_reply_to_reply_sets_component_state_with_mention(): void
    {
        $alice = User::factory()->create(['name' => 'Alice']);
        $bob = User::factory()->create(['name' => 'Bob']);
        $post = Post::factory()->create(['user_id' => $alice->id]);

        $action = app(CreateCommentAction::class);

        // Given: a comment thread with a nested reply
        $parent = $action->execute(
            $alice,
            $post,
            new CreateCommentDTO(content: 'Parent comment', parentCommentId: null)
        );
        $reply = $action->execute(
            $bob,
            $post,
            new CreateCommentDTO(content: 'Reply from Bob', parentCommentId: $parent->id)
        );

        $post->load(['comments.replies.author.profile', 'comments.author.profile', 'comments.reactions', 'comments.replies.reactions']);

        $this->actingAs($alice);

        // When: calling replyToReply on the nested reply
        Volt::test('components.comment-thread', ['post' => $post])
            ->call('replyToReply', $reply->id)
            ->assertSet('reply_to', $parent->id)
            ->assertSet('content', '@Bob ');
    }
}

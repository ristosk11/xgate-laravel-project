<?php

namespace Tests\Unit;

use App\Domain\Content\Models\Post;
use App\Domain\Engagement\Actions\CreateCommentAction;
use App\Domain\Engagement\Actions\UpdateCommentAction;
use App\Domain\Engagement\DTOs\CreateCommentDTO;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateCommentActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_comment_action_updates_comment_content(): void
    {
        $author = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $author->id]);

        $comment = app(CreateCommentAction::class)->execute(
            $author,
            $post,
            new CreateCommentDTO(content: 'Before', parentCommentId: null)
        );

        $updatedComment = app(UpdateCommentAction::class)->execute($comment, 'After');

        $this->assertSame('After', $updatedComment->content);
        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'content' => 'After',
        ]);
    }
}

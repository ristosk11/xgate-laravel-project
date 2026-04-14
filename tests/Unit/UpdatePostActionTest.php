<?php

namespace Tests\Unit;

use App\Domain\Content\Actions\UpdatePostAction;
use App\Domain\Content\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdatePostActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_post_action_updates_post_content(): void
    {
        $author = User::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $author->id,
            'content' => 'Original content',
        ]);

        $updatedPost = app(UpdatePostAction::class)->execute($post, 'Updated content');

        $this->assertSame('Updated content', $updatedPost->content);
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'content' => 'Updated content',
        ]);
    }
}

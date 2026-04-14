<?php

namespace Tests\Feature;

use App\Domain\Content\Models\Post;
use App\Domain\Engagement\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostShowPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_post_show_page_with_comments_section(): void
    {
        $user = User::factory()->createOne();
        $user = User::findOrFail($user->getKey());

        $post = Post::factory()->create([
            'user_id' => $user->id,
            'content' => 'A post content marker',
        ]);

        Comment::factory()->create([
            'post_id' => $post->id,
            'content' => 'A comment marker',
        ]);

        $response = $this->actingAs($user)->get(route('posts.show', ['id' => $post->id]));

        $response
            ->assertOk()
            ->assertSee('A post content marker')
            ->assertSee('Comments');
    }

    public function test_guest_is_redirected_from_post_show_page(): void
    {
        $owner = User::factory()->createOne();
        $owner = User::findOrFail($owner->getKey());

        $post = Post::factory()->create([
            'user_id' => $owner->id,
        ]);

        $response = $this->get(route('posts.show', ['id' => $post->id]));

        $response->assertRedirect(route('login', [], false));
    }

    public function test_post_show_page_renders_comment_content_and_author_name(): void
    {
        $owner = User::factory()->createOne();
        $viewer = User::factory()->createOne();
        $owner = User::findOrFail($owner->getKey());
        $viewer = User::findOrFail($viewer->getKey());

        $post = Post::factory()->create([
            'user_id' => $owner->id,
        ]);

        Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $owner->id,
            'content' => 'A comment marker',
        ]);

        $response = $this->actingAs($viewer)->get(route('posts.show', ['id' => $post->id]));

        $response
            ->assertOk()
            ->assertSee('Comments')
            ->assertSee('A comment marker')
            ->assertSee($owner->name);
    }
}

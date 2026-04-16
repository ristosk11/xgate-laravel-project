<?php

namespace Tests\Feature\Share;

use App\Domain\Content\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SharePostTest extends TestCase
{
    use RefreshDatabase;

    public function test_feed_post_card_exposes_share_url_contract(): void
    {
        $user = User::factory()->createOne();
        $user = User::findOrFail($user->getKey());

        $post = Post::factory()->create([
            'user_id' => $user->id,
            'content' => 'Share contract marker',
        ]);

        $expectedShareUrl = route('posts.show', ['id' => $post->id]);

        $response = $this->actingAs($user)->get(route('feed.index'));

        $response
            ->assertOk()
            ->assertSee('Share contract marker')
            ->assertSee('data-share-url="'.$expectedShareUrl.'"', false);
    }

    public function test_post_show_page_exposes_share_url_contract(): void
    {
        $user = User::factory()->createOne();
        $user = User::findOrFail($user->getKey());

        $post = Post::factory()->create([
            'user_id' => $user->id,
            'content' => 'Share contract marker',
        ]);

        $expectedShareUrl = route('posts.show', ['id' => $post->id]);

        $response = $this->actingAs($user)->get(route('posts.show', ['id' => $post->id]));

        $response
            ->assertOk()
            ->assertSee('Share contract marker')
            ->assertSee('data-share-url="'.$expectedShareUrl.'"', false);
    }
}

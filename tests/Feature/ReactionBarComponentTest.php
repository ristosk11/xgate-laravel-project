<?php

namespace Tests\Feature;

use App\Domain\Content\Models\Post;
use App\Domain\Engagement\Models\Reaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class ReactionBarComponentTest extends TestCase
{
    use RefreshDatabase;

    public function test_reaction_bar_toggle_creates_reaction(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(["user_id" => $user->id]);

        $this->actingAs($user);

        $component = Volt::test("components.reaction-bar", ["reactable" => $post]);

        $component->call("toggle", "like");

        $this->assertDatabaseHas("reactions", [
            "user_id" => $user->id,
            "reactable_type" => Post::class,
            "reactable_id" => $post->id,
            "type" => "like",
        ]);
    }
}

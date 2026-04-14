<?php

namespace Tests\Feature;

use App\Domain\IdentityAndAccess\Actions\ToggleFollowAction;
use App\Domain\IdentityAndAccess\Models\Follow;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class FollowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_follow_and_unfollow_another_user(): void
    {
        $follower = User::factory()->create();
        $following = User::factory()->create();

        $action = app(ToggleFollowAction::class);

        $isFollowing = $action->execute($follower, $following);

        $this->assertTrue($isFollowing);
        $this->assertDatabaseHas('follows', [
            'follower_id' => $follower->id,
            'following_id' => $following->id,
        ]);

        $isFollowing = $action->execute($follower, $following);

        $this->assertFalse($isFollowing);
        $this->assertDatabaseMissing('follows', [
            'follower_id' => $follower->id,
            'following_id' => $following->id,
        ]);
    }

    public function test_user_cannot_follow_self(): void
    {
        $user = User::factory()->create();

        $this->expectException(ValidationException::class);

        app(ToggleFollowAction::class)->execute($user, $user);

        $this->assertSame(0, Follow::query()->count());
    }

    public function test_follow_endpoint_returns_422_for_self_follow(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('profile.follow', ['user' => $user->id]))
            ->assertStatus(422);
    }
}

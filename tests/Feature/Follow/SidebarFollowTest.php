<?php

namespace Tests\Feature\Follow;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SidebarFollowTest extends TestCase
{
    use RefreshDatabase;

    public function test_feed_sidebar_who_to_follow_shows_three_example_users_and_wired_follow_controls(): void
    {
        $viewer = User::factory()->createOne();
        if (! $viewer instanceof User) {
            $this->fail('User factory did not return a User model.');
        }

        $design = User::factory()->createOne(['name' => 'Design']);
        $frontend = User::factory()->createOne(['name' => 'Frontend']);
        $laravel = User::factory()->createOne(['name' => 'Laravel']);

        $response = $this->actingAs($viewer)->get(route('feed.index'));

        $response->assertOk();

        $html = (string) $response->getContent();

        $requiredHandles = ['@design', '@frontend', '@laravel'];
        foreach ($requiredHandles as $handle) {
            $this->assertSame(
                1,
                substr_count($html, $handle),
                sprintf('Expected sidebar to include handle %s exactly once.', $handle)
            );
        }

        $lowerHtml = strtolower($html);

        foreach ([$design, $frontend, $laravel] as $user) {
            $relative = route('profile.follow', ['user' => $user->id], false);
            $absolute = route('profile.follow', ['user' => $user->id]);

            $this->assertStringContainsString(
                'method="post"',
                $lowerHtml,
                'Expected a follow control that POSTs (via a form) to the follow endpoint.'
            );

            $this->assertTrue(
                str_contains($html, 'action="'.$relative.'"') || str_contains($html, 'action="'.$absolute.'"'),
                sprintf('Expected a follow control wired to POST %s (or %s).', $relative, $absolute)
            );
        }
    }
}

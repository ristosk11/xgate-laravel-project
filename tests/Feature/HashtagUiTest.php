<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HashtagUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_feed_does_not_show_laravel12_hashtag_literal(): void
    {
        $user = User::factory()->create();
        if (! $user instanceof User) {
            $this->fail('User factory did not return a User model.');
        }

        $this->actingAs($user);

        $response = $this->get('/feed');

        $response
            ->assertOk()
            ->assertDontSee('#Laravel12');
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DarkModeTest extends TestCase
{
    use RefreshDatabase;

    public function test_dark_mode_toggle_renders_with_alpine_store(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/feed');

        $response->assertStatus(200);

        $response->assertSee('@click="$store.darkMode.toggle()"', false);
        $response->assertSee('$store.darkMode.on', false);
    }

    public function test_dark_mode_toggle_button_exists(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/feed');

        $response->assertStatus(200);

        $response->assertSee("x-text=\"\$store.darkMode.on ? 'Light Mode' : 'Dark Mode'\"", false);
    }
}

<?php

namespace Tests\Unit;

use App\Domain\Content\Models\Post;
use App\Domain\Content\Services\MediaUploadService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaUploadServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_media_upload_service_stores_valid_file_metadata(): void
    {
        Storage::fake('public');

        $author = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $author->id]);
        $image = UploadedFile::fake()->image('post.jpg')->size(2048);

        app(MediaUploadService::class)->storeForPost($post, [$image]);

        $post->refresh();

        $this->assertSame(1, $post->media()->count());
        Storage::disk('public')->assertExists($post->media()->first()->file_path);
    }
}

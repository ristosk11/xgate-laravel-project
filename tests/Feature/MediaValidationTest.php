<?php

namespace Tests\Feature;

use App\Domain\Content\Actions\CreatePostAction;
use App\Domain\Content\DTOs\CreatePostDTO;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class MediaValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_media_validation_rejects_oversized_image_files(): void
    {
        $user = User::factory()->create();
        $bigImage = UploadedFile::fake()->image('huge.jpg')->size(7000);

        $this->expectException(ValidationException::class);

        app(CreatePostAction::class)->execute(
            $user,
            new CreatePostDTO(content: 'hello', media: [$bigImage])
        );
    }

    public function test_media_validation_rejects_oversized_video_files(): void
    {
        $user = User::factory()->create();
        $bigVideo = UploadedFile::fake()->create('huge.webm', 60000, 'video/webm');

        $this->expectException(ValidationException::class);

        app(CreatePostAction::class)->execute(
            $user,
            new CreatePostDTO(content: 'video', media: [$bigVideo])
        );
    }
}

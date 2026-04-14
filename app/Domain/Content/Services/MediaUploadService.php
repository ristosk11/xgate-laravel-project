<?php

namespace App\Domain\Content\Services;

use App\Domain\Content\Enums\MediaType;
use App\Domain\Content\Models\Post;
use App\Domain\Content\Models\PostMedia;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;

final class MediaUploadService
{
    public function storeForPost(Post $post, array $files): void
    {
        foreach ($files as $index => $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            $this->validateFile($file);

            $mediaType = str_starts_with($file->getMimeType() ?? '', 'video/')
                ? MediaType::Video
                : MediaType::Image;

            $path = $file->store('posts', 'public');

            PostMedia::query()->create([
                'post_id' => $post->id,
                'file_path' => $path,
                'type' => $mediaType,
                'display_order' => $index,
                'alt_text' => null,
            ]);
        }
    }

    private function validateFile(UploadedFile $file): void
    {
        Validator::validate(
            ['file' => $file],
            ['file' => ['required', 'file', 'mimetypes:image/jpeg,image/png,image/webp,video/mp4,video/webm', 'max:51200']]
        );

        if (str_starts_with($file->getMimeType() ?? '', 'image/') && $file->getSize() > (5 * 1024 * 1024)) {
            Validator::make([], [])->after(function ($validator): void {
                $validator->errors()->add('file', __('Image files must be 5MB or less.'));
            })->validate();
        }
    }
}

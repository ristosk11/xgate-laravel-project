<?php

namespace App\Domain\Content\Actions;

use App\Domain\Content\DTOs\CreatePostDTO;
use App\Domain\Content\Models\Post;
use App\Domain\Content\Services\MediaUploadService;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class CreatePostAction
{
    public function __construct(private readonly MediaUploadService $mediaUploadService) {}

    public function execute(User $author, CreatePostDTO $dto): Post
    {
        return DB::transaction(function () use ($author, $dto): Post {
            $post = Post::query()->create([
                'user_id' => $author->id,
                'content' => $dto->content,
            ]);

            if ($dto->media !== []) {
                $this->mediaUploadService->storeForPost($post, $dto->media);
            }

            return $post->refresh();
        });
    }
}

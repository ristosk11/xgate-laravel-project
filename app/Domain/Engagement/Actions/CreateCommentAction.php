<?php

namespace App\Domain\Engagement\Actions;

use App\Domain\Content\Models\Post;
use App\Domain\Engagement\DTOs\CreateCommentDTO;
use App\Domain\Engagement\Models\Comment;
use App\Models\User;

final class CreateCommentAction
{
    public function execute(User $author, Post $post, CreateCommentDTO $dto): Comment
    {
        $parent = null;

        if ($dto->parentCommentId !== null) {
            $parent = Comment::query()->where('post_id', $post->id)->findOrFail($dto->parentCommentId);

            if ($parent->parent_comment_id !== null) {
                $parent = Comment::query()->findOrFail($parent->parent_comment_id);
            }
        }

        return Comment::query()->create([
            'user_id' => $author->id,
            'post_id' => $post->id,
            'parent_comment_id' => $parent?->id,
            'content' => $dto->content,
        ]);
    }
}

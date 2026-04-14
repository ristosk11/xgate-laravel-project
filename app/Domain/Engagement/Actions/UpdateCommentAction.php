<?php

namespace App\Domain\Engagement\Actions;

use App\Domain\Engagement\Models\Comment;

final class UpdateCommentAction
{
    public function execute(Comment $comment, string $content): Comment
    {
        $comment->update([
            'content' => $content,
        ]);

        return $comment->refresh();
    }
}

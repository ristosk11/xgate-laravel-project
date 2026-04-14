<?php

namespace App\Domain\Content\Actions;

use App\Domain\Content\Models\Post;

final class UpdatePostAction
{
    public function execute(Post $post, ?string $content): Post
    {
        $post->update([
            'content' => $content,
        ]);

        return $post->refresh();
    }
}

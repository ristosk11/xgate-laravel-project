<?php

namespace App\Domain\Content\Actions;

use App\Domain\Content\Models\Post;
use App\Domain\Engagement\Models\Comment;
use App\Domain\Engagement\Models\Reaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

final class DeletePostAction
{
    public function execute(Post $post): void
    {
        DB::transaction(function () use ($post): void {
            $post->loadMissing(['media', 'comments.replies']);

            foreach ($post->media as $media) {
                Storage::disk('public')->delete($media->file_path);
            }

            $allCommentIds = $post->comments
                ->flatMap(fn (Comment $comment) => $comment->replies->pluck('id')->push($comment->id));

            if ($allCommentIds->isNotEmpty()) {
                Reaction::query()
                    ->where('reactable_type', Comment::class)
                    ->whereIn('reactable_id', $allCommentIds->unique()->values())
                    ->delete();
            }

            Reaction::query()
                ->where('reactable_type', Post::class)
                ->where('reactable_id', $post->id)
                ->delete();

            $post->delete();
        });
    }
}

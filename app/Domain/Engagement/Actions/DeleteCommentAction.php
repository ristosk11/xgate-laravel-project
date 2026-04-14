<?php

namespace App\Domain\Engagement\Actions;

use App\Domain\Engagement\Models\Comment;
use App\Domain\Engagement\Models\Reaction;
use Illuminate\Support\Facades\DB;

final class DeleteCommentAction
{
    public function execute(Comment $comment): void
    {
        DB::transaction(function () use ($comment): void {
            $replyIds = $comment->replies()->pluck('id');
            $ids = $replyIds->push($comment->id);

            Reaction::query()
                ->where('reactable_type', Comment::class)
                ->whereIn('reactable_id', $ids)
                ->delete();

            Comment::query()->whereIn('id', $ids)->delete();
        });
    }
}

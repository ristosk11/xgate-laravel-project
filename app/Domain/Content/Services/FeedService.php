<?php

namespace App\Domain\Content\Services;

use App\Domain\Content\Models\Post;
use App\Domain\Engagement\Models\Reaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

final class FeedService
{
    public function getFeed(User $user, int $perPage = 15): LengthAwarePaginator
    {
        $followedIds = $user->followsAsFollower()->pluck('following_id');

        if ($followedIds->isEmpty()) {
            return $this->attachReactionData($this->discoverFeed($perPage), $user);
        }

        $feed = Post::query()
            ->whereIn('user_id', $followedIds)
            ->with([
                'author.profile',
                'media',
            ])
            ->withCount('comments')
            ->withCount([
                'reactions as reactions_count' => fn (Builder $query): Builder => $query,
            ])
            ->latest()
            ->paginate($perPage);

        return $this->attachReactionData($feed, $user);
    }

    public function reactionSummary(Post $post): Collection
    {
        return $post->reactions()
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type');
    }

    private function discoverFeed(int $perPage): LengthAwarePaginator
    {
        return Post::query()
            ->with([
                'author.profile',
                'media',
            ])
            ->withCount('comments')
            ->withCount([
                'reactions as reactions_count' => fn (Builder $query): Builder => $query,
            ])
            ->orderByDesc('reactions_count')
            ->latest()
            ->paginate($perPage);
    }

    private function attachReactionData(LengthAwarePaginator $paginator, User $user): LengthAwarePaginator
    {
        $posts = collect($paginator->items());
        $postIds = $posts->pluck('id')->values();

        if ($postIds->isEmpty()) {
            return $paginator;
        }

        $groupedCounts = Reaction::query()
            ->where('reactable_type', Post::class)
            ->whereIn('reactable_id', $postIds)
            ->selectRaw('reactable_id, type, COUNT(*) as count')
            ->groupBy('reactable_id', 'type')
            ->get()
            ->groupBy('reactable_id')
            ->map(function (Collection $rows): array {
                return $rows
                    ->mapWithKeys(function ($row): array {
                        $type = $row->type;
                        $key = $type instanceof \BackedEnum ? $type->value : (string) $type;

                        return [$key => (int) $row->count];
                    })
                    ->all();
            });

        $userReactions = Reaction::query()
            ->where('reactable_type', Post::class)
            ->whereIn('reactable_id', $postIds)
            ->where('user_id', $user->id)
            ->get(['reactable_id', 'type'])
            ->mapWithKeys(function ($row): array {
                $type = $row->type;
                $value = $type instanceof \BackedEnum ? $type->value : (string) $type;

                return [$row->reactable_id => $value];
            });

        $posts->each(function (Post $post) use ($groupedCounts, $userReactions): void {
            $post->setAttribute('reaction_summary', $groupedCounts->get($post->id, []));
            $post->setAttribute('current_user_reaction', $userReactions->get($post->id));
        });

        $paginator->setCollection($posts);

        return $paginator;
    }
}

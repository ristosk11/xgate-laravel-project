<?php

namespace App\Domain\Engagement\Actions;

use App\Domain\Engagement\Enums\ReactionType;
use App\Domain\Engagement\Models\Reaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

final class ToggleReactionAction
{
    public function execute(User $user, Model $reactable, ReactionType $type): Collection
    {
        $reaction = Reaction::query()
            ->where('user_id', $user->id)
            ->where('reactable_type', $reactable::class)
            ->where('reactable_id', $reactable->getKey())
            ->first();

        if ($reaction === null) {
            Reaction::query()->create([
                'user_id' => $user->id,
                'reactable_type' => $reactable::class,
                'reactable_id' => $reactable->getKey(),
                'type' => $type,
            ]);
        } elseif ($reaction->type === $type) {
            $reaction->delete();
        } else {
            $reaction->update(['type' => $type]);
        }

        return Reaction::query()
            ->where('reactable_type', $reactable::class)
            ->where('reactable_id', $reactable->getKey())
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type');
    }
}

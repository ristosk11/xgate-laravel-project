<?php

namespace App\Domain\Engagement\Services;

use App\Domain\Engagement\Models\Reaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

final class ReactionCountService
{
    public function groupedCounts(Model $reactable): Collection
    {
        return Reaction::query()
            ->where('reactable_type', $reactable::class)
            ->where('reactable_id', $reactable->getKey())
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type');
    }
}

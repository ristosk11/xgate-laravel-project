<?php

namespace App\Domain\IdentityAndAccess\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

final class FollowService
{
    public function followers(User $user): Collection
    {
        return User::query()
            ->whereIn('id', $user->followsAsFollowing()->select('follower_id'))
            ->with('profile')
            ->get();
    }

    public function following(User $user): Collection
    {
        return User::query()
            ->whereIn('id', $user->followsAsFollower()->select('following_id'))
            ->with('profile')
            ->get();
    }
}

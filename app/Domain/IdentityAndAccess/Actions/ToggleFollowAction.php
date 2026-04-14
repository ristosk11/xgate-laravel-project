<?php

namespace App\Domain\IdentityAndAccess\Actions;

use App\Domain\IdentityAndAccess\Models\Follow;
use App\Models\User;
use Illuminate\Validation\ValidationException;

final class ToggleFollowAction
{
    public function execute(User $follower, User $following): bool
    {
        if ($follower->is($following)) {
            throw ValidationException::withMessages([
                'following_id' => __('You cannot follow yourself.'),
            ]);
        }

        $existing = Follow::query()
            ->where('follower_id', $follower->id)
            ->where('following_id', $following->id)
            ->first();

        if ($existing !== null) {
            $existing->delete();

            return false;
        }

        Follow::query()->create([
            'follower_id' => $follower->id,
            'following_id' => $following->id,
        ]);

        return true;
    }
}

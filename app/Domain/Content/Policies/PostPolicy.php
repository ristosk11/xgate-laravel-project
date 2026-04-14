<?php

namespace App\Domain\Content\Policies;

use App\Domain\Content\Models\Post;
use App\Models\User;

final class PostPolicy
{
    public function update(User $user, Post $post): bool
    {
        return $post->user_id === $user->id;
    }

    public function delete(User $user, Post $post): bool
    {
        return $post->user_id === $user->id;
    }
}

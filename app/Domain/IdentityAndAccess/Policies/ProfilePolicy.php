<?php

namespace App\Domain\IdentityAndAccess\Policies;

use App\Domain\IdentityAndAccess\Models\Profile;
use App\Models\User;

final class ProfilePolicy
{
    public function update(User $user, Profile $profile): bool
    {
        return $profile->user_id === $user->id;
    }
}

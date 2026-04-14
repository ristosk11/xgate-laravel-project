<?php

namespace App\Domain\IdentityAndAccess\Actions;

use App\Domain\IdentityAndAccess\DTOs\UpdateProfileDTO;
use App\Domain\IdentityAndAccess\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class UpdateProfileAction
{
    public function execute(User $user, UpdateProfileDTO $dto): Profile
    {
        return DB::transaction(function () use ($user, $dto): Profile {
            $profile = $user->profile()->firstOrCreate([], [
                'bio' => null,
                'location' => null,
                'website' => null,
                'avatar_path' => null,
                'cover_image_path' => null,
            ]);

            $profile->fill([
                'bio' => $dto->bio,
                'location' => $dto->location,
                'website' => $dto->website,
                'avatar_path' => $dto->avatarPath ?? $profile->avatar_path,
                'cover_image_path' => $dto->coverImagePath ?? $profile->cover_image_path,
            ]);

            $profile->save();

            return $profile->refresh();
        });
    }
}

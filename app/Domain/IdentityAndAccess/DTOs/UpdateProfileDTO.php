<?php

namespace App\Domain\IdentityAndAccess\DTOs;

final class UpdateProfileDTO
{
    public function __construct(
        public readonly ?string $bio,
        public readonly ?string $location,
        public readonly ?string $website,
        public readonly ?string $avatarPath,
        public readonly ?string $coverImagePath,
    ) {}
}

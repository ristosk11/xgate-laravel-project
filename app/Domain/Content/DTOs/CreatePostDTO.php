<?php

namespace App\Domain\Content\DTOs;

final class CreatePostDTO
{
    public function __construct(
        public readonly ?string $content,
        public readonly array $media,
    ) {}
}

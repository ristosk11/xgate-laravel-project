<?php

namespace App\Domain\Engagement\DTOs;

final class CreateCommentDTO
{
    public function __construct(
        public readonly string $content,
        public readonly ?string $parentCommentId,
    ) {}
}

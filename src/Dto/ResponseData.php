<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class ResponseData
{
    public function __construct(
        #[Assert\NotBlank]
        public string             $responseId,

        #[Assert\NotBlank]
        public string             $responder,

        #[Assert\NotNull]
        #[Assert\Type(\DateTimeImmutable::class)]
        public \DateTimeImmutable $repliedAt,

        #[Assert\NotBlank]
        public string             $content,
    )
    {
    }
}

<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class ReviewData
{
    /**
     * @param ResponseData[]|null $responses
     */
    public function __construct(
        #[Assert\NotBlank]
        public string             $reviewId,

        #[Assert\NotBlank]
        public string             $status,

        #[Assert\NotBlank]
        public string             $platform,

        #[Assert\NotBlank]
        public string             $hotelId,

        #[Assert\NotBlank]
        public string             $guestName,

        #[Assert\NotNull]
        #[Assert\Type(\DateTimeImmutable::class)]
        public \DateTimeImmutable $submittedAt,

        #[Assert\NotBlank]
        public string             $content,

        #[Assert\NotNull]
        public int                $rating,

        #[Assert\NotBlank]
        public string             $language,

        #[Assert\Type('array')]
        #[Assert\Valid]
        public ?array             $responses,
    )
    {
    }
}


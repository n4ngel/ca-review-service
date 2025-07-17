<?php
declare(strict_types=1);

namespace App\Dto;

use DateTimeInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class ReviewListQuery
{
    #[Assert\Optional]
    #[Assert\DateTime(format: DateTimeInterface::ATOM)]
    public ?string $submittedAfter = null;

    #[Assert\Optional]
    #[Assert\DateTime(format: DateTimeInterface::ATOM)]
    public ?string $submittedBefore = null;

    #[Assert\Optional]
    #[Assert\Type('string')]
    public ?string $hotelId = null;
}

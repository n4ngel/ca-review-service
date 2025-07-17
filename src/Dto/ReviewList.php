<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\SerializedName;

final class ReviewList
{
    #[SerializedName('review_list')]
    #[Assert\NotNull]
    #[Assert\Type('array')]
    #[Assert\All([
        new Assert\Type(ReviewData::class),
        new Assert\Valid()
    ])]
    private array $reviews;

    /**
     * @param ReviewData[] $reviews
     */
    public function __construct(array $reviews)
    {
        $this->reviews = $reviews;
    }

    public function getReviews(): array
    {
        return $this->reviews;
    }
}

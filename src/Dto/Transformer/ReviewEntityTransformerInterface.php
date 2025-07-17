<?php

namespace App\Dto\Transformer;

use App\Dto\ReviewData;
use App\Entity\Review;

interface ReviewEntityTransformerInterface
{
    public function transform(ReviewData $reviewDto, Review $review): Review;
}

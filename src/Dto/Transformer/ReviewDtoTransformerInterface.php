<?php
declare(strict_types=1);

namespace App\Dto\Transformer;

use App\Dto\ReviewData;
use App\Entity\Review;

interface ReviewDtoTransformerInterface
{
    public function transform(Review $review): ReviewData;
}

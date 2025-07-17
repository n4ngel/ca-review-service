<?php
declare(strict_types=1);

namespace App\Dto\Transformer;

use App\Dto\ReviewData;
use App\Dto\ResponseData;
use App\Entity\Review;

final class ReviewDtoTransformer implements ReviewDtoTransformerInterface
{
    public function transform(Review $review): ReviewData
    {
        $responses = array_map(
            fn($resp) => new ResponseData(
                $resp->getResponseId(),
                $resp->getResponder(),
                $resp->getRepliedAt(),
                $resp->getContent(),
            ),
            $review->getResponses()->toArray()
        );

        return new ReviewData(
            $review->getReviewId(),
            $review->getStatus(),
            $review->getPlatform(),
            $review->getHotelId(),
            $review->getGuestName(),
            $review->getSubmittedAt(),
            $review->getContent(),
            $review->getRating(),
            $review->getLanguage(),
            $responses,
        );
    }
}

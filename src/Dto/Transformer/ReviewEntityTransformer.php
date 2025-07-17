<?php

declare(strict_types=1);

namespace App\Dto\Transformer;

use App\Dto\ReviewData;
use App\Entity\Review;
use App\Entity\ReviewResponse;

class ReviewEntityTransformer implements ReviewEntityTransformerInterface
{
    public function transform(ReviewData $reviewDto, Review $review): Review
    {

        $review
            ->setReviewId($reviewDto->reviewId)
            ->setStatus($reviewDto->status)
            ->setPlatform($reviewDto->platform)
            ->setHotelId($reviewDto->hotelId)
            ->setGuestName($reviewDto->guestName)
            ->setSubmittedAt($reviewDto->submittedAt)
            ->setContent($reviewDto->content)
            ->setRating($reviewDto->rating)
            ->setLanguage($reviewDto->language);

        //index incoming responses by ID
        $incomingById = [];
        foreach ($reviewDto->responses ?? [] as $responseDto) {
            $incomingById[$responseDto->responseId] = $responseDto;
        }

        //remove any entity responses no longer in DTO
        foreach ($review->getResponses() as $existing) {
            if (!isset($incomingById[$existing->getResponseId()])) {
                $review->removeResponse($existing);
            }
        }

        //update or create remaining responses
        foreach ($incomingById as $id => $responseDto) {
            $existing = $review
                ->getResponses()
                ->filter(fn(ReviewResponse $r) => $r->getResponseId() === $id)
                ->first();

            if ($existing) {
                $existing
                    ->setResponder($responseDto->responder)
                    ->setRepliedAt($responseDto->repliedAt)
                    ->setContent($responseDto->content);
            } else {
                $new = (new ReviewResponse())
                    ->setResponseId($responseDto->responseId)
                    ->setResponder($responseDto->responder)
                    ->setRepliedAt($responseDto->repliedAt)
                    ->setContent($responseDto->content);
                $review->addResponse($new);
            }
        }

        return $review;
    }
}

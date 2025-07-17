<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\ReviewData;
use App\Dto\Transformer\ReviewEntityTransformerInterface;
use App\Enum\ReviewStatus;
use App\Provider\ReviewProviderInterface;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Review;

final readonly class ReviewDataSyncService
{
    public function __construct(
        private ReviewRepository                 $repo,
        private ReviewEntityTransformerInterface $reviewEntityTransformer,
        private EntityManagerInterface           $em,
    ) {}

    public function sync(ReviewProviderInterface $provider): void
    {
        $reviewsData = $provider->fetchReviews();

        foreach ($reviewsData as $reviewDto) {
            $existingReview = $this->repo->findOneByReviewId($reviewDto->reviewId);

            if ($this->shouldSkip($reviewDto, $existingReview)) {
                continue;
            }

            $review = $existingReview ?? new Review();
            $this->reviewEntityTransformer->transform($reviewDto, $review);
            $this->em->persist($review);
        }

        $this->em->flush();
    }

    private function shouldSkip(ReviewData $reviewDto, ?Review $review): bool
    {
        //new reviews must have the status 'published'
        if (null === $review) {
            return $reviewDto->status !== ReviewStatus::PUBLISHED->value;
        }

        //existing reviews are only updated if newer
        return $reviewDto->submittedAt <= $review->getSubmittedAt();
    }

}

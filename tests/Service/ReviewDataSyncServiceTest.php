<?php

namespace App\Tests\Service;

use App\Dto\ReviewData;
use App\Dto\Transformer\ReviewEntityTransformerInterface;
use App\Entity\Review;
use App\Enum\ReviewStatus;
use App\Provider\ReviewProviderInterface;
use App\Repository\ReviewRepository;
use App\Service\ReviewDataSyncService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class ReviewDataSyncServiceTest extends TestCase
{
    public function testSyncWithNewPublishedReview(): void
    {
        $reviewData = new ReviewData(
            'review1',
            ReviewStatus::PUBLISHED->value,
            'platform1',
            'hotel1',
            'guest1',
            new \DateTimeImmutable('2025-07-01'),
            'Great place!',
            5,
            'en',
            []
        );

        $provider = $this->createMock(ReviewProviderInterface::class);
        $provider->method('fetchReviews')->willReturn([$reviewData]);

        $repo = $this->createMock(ReviewRepository::class);
        $repo->method('findOneByReviewId')->with('review1')->willReturn(null);

        $reviewEntityTransformer = $this->createMock(ReviewEntityTransformerInterface::class);
        $reviewEntityTransformer
            ->expects($this->once())
            ->method('transform')
            ->with($reviewData, $this->isInstanceOf(Review::class));

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())->method('persist');
        $entityManager->expects($this->once())->method('flush');

        $service = new ReviewDataSyncService($repo, $reviewEntityTransformer, $entityManager);
        $service->sync($provider);
    }

    public function testSyncSkipsNonPublishedNewReview(): void
    {
        $reviewData = new ReviewData(
            'review2',
            ReviewStatus::REMOVED->value,
            'platform1',
            'hotel1',
            'guest2',
            new \DateTimeImmutable('2025-07-02'),
            'Not so great',
            3,
            'en',
            []
        );

        $provider = $this->createMock(ReviewProviderInterface::class);
        $provider->method('fetchReviews')->willReturn([$reviewData]);

        $repo = $this->createMock(ReviewRepository::class);
        $repo->method('findOneByReviewId')->with('review2')->willReturn(null);

        $reviewEntityTransformer = $this->createMock(ReviewEntityTransformerInterface::class);
        $reviewEntityTransformer->expects($this->never())->method('transform');

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->never())->method('persist');
        $entityManager->expects($this->once())->method('flush');

        $service = new ReviewDataSyncService($repo, $reviewEntityTransformer, $entityManager);
        $service->sync($provider);
    }

    public function testSyncUpdatesExistingReviewIfNewer(): void
    {
        $existingReview = (new Review())
            ->setReviewId('review3')
            ->setSubmittedAt(new \DateTimeImmutable('2025-06-01'));

        $reviewData = new ReviewData(
            'review3',
            ReviewStatus::PUBLISHED->value,
            'platform1',
            'hotel1',
            'guest3',
            new \DateTimeImmutable('2025-07-03'),
            'Updated review',
            4,
            'fr',
            []
        );

        $provider = $this->createMock(ReviewProviderInterface::class);
        $provider->method('fetchReviews')->willReturn([$reviewData]);

        $repo = $this->createMock(ReviewRepository::class);
        $repo->method('findOneByReviewId')->with('review3')->willReturn($existingReview);

        $reviewEntityTransformer = $this->createMock(ReviewEntityTransformerInterface::class);
        $reviewEntityTransformer
            ->expects($this->once())
            ->method('transform')
            ->with($reviewData, $existingReview);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())->method('persist');
        $entityManager->expects($this->once())->method('flush');

        $service = new ReviewDataSyncService($repo, $reviewEntityTransformer, $entityManager);
        $service->sync($provider);
    }

    public function testSyncSkipsExistingReviewIfOlder(): void
    {
        $existingReview = (new Review())
            ->setReviewId('review4')
            ->setSubmittedAt(new \DateTimeImmutable('2025-07-04'));

        $reviewData = new ReviewData(
            'review4',
            ReviewStatus::PUBLISHED->value,
            'platform2',
            'hotel2',
            'guest4',
            new \DateTimeImmutable('2025-07-01'),
            'Older review',
            2,
            'de',
            []
        );

        $provider = $this->createMock(ReviewProviderInterface::class);
        $provider->method('fetchReviews')->willReturn([$reviewData]);

        $repo = $this->createMock(ReviewRepository::class);
        $repo->method('findOneByReviewId')->with('review4')->willReturn($existingReview);

        $reviewEntityTransformer = $this->createMock(ReviewEntityTransformerInterface::class);
        $reviewEntityTransformer->expects($this->never())->method('transform');

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->never())->method('persist');
        $entityManager->expects($this->once())->method('flush');

        $service = new ReviewDataSyncService($repo, $reviewEntityTransformer, $entityManager);
        $service->sync($provider);
    }
}

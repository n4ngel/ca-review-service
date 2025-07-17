<?php

namespace App\Repository;

use App\Entity\Review;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Review>
 */
class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }
    public function findOneByReviewId(string $reviewId)
    {
        return $this->createQueryBuilder('r')

            ->andWhere('r.reviewId = :val')
            ->setParameter('val', $reviewId)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    /**
     * Fetch only published reviews, optionally filtered by date range and hotel.
     *
     * @param \DateTimeImmutable|null $after
     * @param \DateTimeImmutable|null $before
     * @param string|null            $hotelId
     * @return Review[]
     */
    public function findPublishedInDateRange(
        ?\DateTimeImmutable $after,
        ?\DateTimeImmutable $before,
        ?string $hotelId
    ): array {
        $qb = $this->createQueryBuilder('r')
            ->andWhere('r.status = :published')
            ->setParameter('published', 'published');

        if (null !== $after) {
            $qb->andWhere('r.submittedAt > :after')
                ->setParameter('after', $after);
        }

        if (null !== $before) {
            $qb->andWhere('r.submittedAt < :before')
                ->setParameter('before', $before);
        }

        if (null !== $hotelId) {
            $qb->andWhere('r.hotelId = :hotel')
                ->setParameter('hotel', $hotelId);
        }

        return $qb
            ->orderBy('r.submittedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

}

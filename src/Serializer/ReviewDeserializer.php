<?php
declare(strict_types=1);

namespace App\Serializer;

use App\Dto\ReviewData;
use App\Dto\ReviewList;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ReviewDeserializer implements ReviewDeserializerInterface
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface  $validator,
        private readonly LoggerInterface     $logger,
    ) {}

    /**
     * @param string $json
     * @return ReviewData[]    only the valid reviews
     * @throws ExceptionInterface
     */
    public function fromJson(string $json): array
    {
        /** @var ReviewList $reviewList */
        $reviewList = $this->serializer->deserialize(
            $json,
            ReviewList::class,
            'json'
        );

        $valid = [];
        foreach ($reviewList->getReviews() as $idx => $review) {
            $violations = $this->validator->validate($review);
            if (0 === count($violations)) {
                $valid[] = $review;
                continue;
            }

            $messages = [];
            foreach ($violations as $v) {
                $messages[] = sprintf(
                    '[%s] %s → %s',
                    $v->getPropertyPath(),
                    json_encode($v->getInvalidValue()),
                    $v->getMessage()
                );
            }
            $this->logger->warning(
                sprintf('Skipping Review #%d due to validation errors:', $idx),
                $messages
            );
        }

        return $valid;
    }
}

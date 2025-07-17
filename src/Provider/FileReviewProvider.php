<?php

declare(strict_types=1);

namespace App\Provider;

use App\Serializer\ReviewDeserializerInterface;
use App\Dto\ReviewData;

final readonly class FileReviewProvider implements ReviewProviderInterface
{
    public function __construct(
        private ReviewDeserializerInterface $deserializer,
    ) {}

    /**
     * @return ReviewData[]
     */
    public function fetchReviews(): array
    {
        $json = file_get_contents(__DIR__  . '/data/response3.json');
        return $this->deserializer->fromJson($json);
    }

    public function getName(): string
    {
        return 'file';
    }
}

<?php

declare(strict_types=1);

namespace App\Provider;

use App\Dto\ReviewData;

interface ReviewProviderInterface
{
    /**
     * @return ReviewData[]
     */
    public function fetchReviews(): array;

    public function getName(): string;
}

<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Dto\ReviewData;

interface ReviewDeserializerInterface
{
    /**
     * Deserialize JSON into an array of valid ReviewData DTOs.
     *
     * @param string $json
     * @return ReviewData[]
     */
    public function fromJson(string $json): array;

}

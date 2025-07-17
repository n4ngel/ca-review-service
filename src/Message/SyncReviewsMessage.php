<?php
declare(strict_types=1);

namespace App\Message;

final class SyncReviewsMessage
{
    public function __construct(public string $providerName) {}
}

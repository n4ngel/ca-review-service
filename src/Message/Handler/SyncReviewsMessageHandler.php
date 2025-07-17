<?php
declare(strict_types=1);

namespace App\Message\Handler;

use App\Message\SyncReviewsMessage;
use App\Service\ProviderRegistry;
use App\Service\ReviewDataSyncService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SyncReviewsMessageHandler
{
    public function __construct(
        private ProviderRegistry     $registry,
        private ReviewDataSyncService $syncService,
    ) {}

    public function __invoke(SyncReviewsMessage $msg): void
    {
        $provider = $this->registry->get($msg->providerName);
        $this->syncService->sync($provider);
    }
}

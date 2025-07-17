<?php

declare(strict_types=1);

namespace App\Controller;

use App\Message\SyncReviewsMessage;
use App\Service\ProviderRegistry;
use App\Service\ReviewDataSyncService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

final class ReviewController extends AbstractController
{
    public function __construct(
        private MessageBusInterface $bus,
        private ReviewDataSyncService $syncService,
        private ProviderRegistry     $registry,
    )
    {
    }
    #[Route('/review', name: 'app_review')]
    public function index(): JsonResponse
    {
        $provider = $this->registry->get('file');
        $this->syncService->sync($provider);
        //$this->bus->dispatch(new SyncReviewsMessage('file'));
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ReviewController.php',
        ]);
    }
}

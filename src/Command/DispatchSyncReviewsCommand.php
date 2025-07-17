<?php
declare(strict_types=1);

namespace App\Command;

use App\Message\SyncReviewsMessage;
use App\Service\ProviderRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(name: 'app:sync-reviews')]
final class DispatchSyncReviewsCommand extends Command
{
    public function __construct(
        private ProviderRegistry    $registry,
        private MessageBusInterface $bus,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $in, OutputInterface $out): int
    {
        foreach ($this->registry->all() as $name => $_) {
            $this->bus->dispatch(new SyncReviewsMessage($name));
            $out->writeln("→ Dispatched sync for “{$name}”");
        }
        return Command::SUCCESS;
    }
}

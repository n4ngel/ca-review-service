<?php
declare(strict_types=1);

namespace App\Service;

use App\Provider\ReviewProviderInterface;
use InvalidArgumentException;

final class ProviderRegistry
{
    /** @var array<string,ReviewProviderInterface> */
    private array $providers = [];

    public function __construct(iterable $providers)
    {
        foreach ($providers as $p) {
            $this->providers[$p->getName()] = $p;
        }
    }

    public function get(string $name): ReviewProviderInterface
    {
        if (!isset($this->providers[$name])) {
            throw new InvalidArgumentException("No review provider named “{$name}”");
        }
        return $this->providers[$name];
    }

    /** @return ReviewProviderInterface[] */
    public function all(): array
    {
        return $this->providers;
    }
}

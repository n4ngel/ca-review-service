<?php

namespace App\Tests\Service;

use App\Provider\ReviewProviderInterface;
use App\Service\ProviderRegistry;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ProviderRegistryTest extends TestCase
{
    public function testGetReturnsProviderWhenNameExists(): void
    {
        $mockProvider = $this->createMock(ReviewProviderInterface::class);
        $mockProvider->method('getName')->willReturn('provider_one');

        $registry = new ProviderRegistry([$mockProvider]);

        $retrievedProvider = $registry->get('provider_one');

        $this->assertSame($mockProvider, $retrievedProvider);
    }

    public function testGetThrowsExceptionWhenNameDoesNotExist(): void
    {
        $mockProvider = $this->createMock(ReviewProviderInterface::class);
        $mockProvider->method('getName')->willReturn('provider_one');

        $registry = new ProviderRegistry([$mockProvider]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('No review provider named “provider_two”');

        $registry->get('provider_two');
    }
}

<?php

namespace OneToMany\PdfPack\Tests\Factory;

use OneToMany\PdfPack\Client\Mock\MockExtractorClient;
use OneToMany\PdfPack\Factory\Exception\CreatingExtractorClientFailedServiceNotFoundException;
use OneToMany\PdfPack\Factory\ExtractorClientFactory;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

#[Group('UnitTests')]
#[Group('FactoryTests')]
final class ExtractorClientFactoryTest extends TestCase
{
    public function testCreatingServiceRequiresServiceToExist(): void
    {
        $this->expectException(CreatingExtractorClientFailedServiceNotFoundException::class);

        new ExtractorClientFactory($this->createContainer())->create('invalid');
    }

    public function testCreatingServiceRequiresServiceToImplementRasterServiceInterface(): void
    {
        $this->expectException(CreatingExtractorClientFailedServiceNotFoundException::class);

        new ExtractorClientFactory($this->createContainer())->create('error');
    }

    private function createContainer(): ContainerInterface
    {
        $container = new class implements ContainerInterface {
            /**
             * @var array{
             *   mock: MockExtractorClient,
             *   error: \InvalidArgumentException,
             * }
             */
            private array $services;

            public function __construct()
            {
                $this->services = [
                    'mock' => new MockExtractorClient(),
                    'error' => new \InvalidArgumentException(),
                ];
            }

            public function get(string $id): mixed
            {
                return $this->services[$id] ?? null;
            }

            public function has(string $id): bool
            {
                return isset($this->services[$id]);
            }
        };

        return $container;
    }
}

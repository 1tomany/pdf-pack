<?php

namespace OneToMany\PdfPack\Factory;

use OneToMany\PdfPack\Contract\Client\ClientInterface;
use OneToMany\PdfPack\Factory\Exception\CreatingClientFailedServiceNotFoundException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

final readonly class ClientFactory
{
    public function __construct(private ContainerInterface $container)
    {
    }

    public function create(string $service): ClientInterface
    {
        try {
            $client = $this->container->get($service);

            if (!$client instanceof ClientInterface) {
                throw new CreatingClientFailedServiceNotFoundException($service);
            }
        } catch (ContainerExceptionInterface $e) {
            throw new CreatingClientFailedServiceNotFoundException($service, $e);
        }

        return $client;
    }
}

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

    public function create(string $id): ClientInterface
    {
        try {
            $service = $this->container->get($id);

            if (!$service instanceof ClientInterface) {
                throw new CreatingClientFailedServiceNotFoundException($id);
            }
        } catch (ContainerExceptionInterface $e) {
            throw new CreatingClientFailedServiceNotFoundException($id, $e);
        }

        return $service;
    }
}

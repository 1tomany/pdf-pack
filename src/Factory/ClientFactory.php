<?php

namespace OneToMany\PdfPack\Factory;

use OneToMany\PdfPack\Contract\Client\ClientInterface;
use OneToMany\PdfPack\Exception\InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

final readonly class ClientFactory
{
    public function __construct(
        private ContainerInterface $container = new ClientContainer(),
    )
    {
    }

    /**
     * @throws InvalidArgumentException when a vendor does not have a registered client
     */
    public function create(string $vendor): ClientInterface
    {
        try {
            $client = $this->container->get($vendor);
        } catch (ContainerExceptionInterface $e) {
        }

        if (!isset($client) || !$client instanceof ClientInterface) {
            throw new InvalidArgumentException(\sprintf('The vendor "%s" does not have a registered client.', $vendor), previous: $e ?? null);
        }

        return $client;
    }
}

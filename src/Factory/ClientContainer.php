<?php

namespace OneToMany\PdfPack\Factory;

use OneToMany\PdfPack\Contract\Client\ClientInterface;
use OneToMany\PdfPack\Factory\Exception\ContainerEntryNotFoundException;
use Psr\Container\ContainerInterface;

use function array_key_exists;
use function sprintf;

final class ClientContainer implements ContainerInterface
{
    /**
     * @var array<non-empty-string, ClientInterface>
     */
    private array $clients = [];

    /**
     * @param list<ClientInterface> $clients
     */
    public function __construct(
        array $clients = [],
    )
    {
        foreach ($clients as $client) {
            $this->addClient($client);
        }
    }

    public function addClient(ClientInterface $client): static
    {
        $this->clients[$client::getVendor()] = $client;

        return $this;
    }

    /**
     * @see Psr\Container\ContainerInterface
     */
    public function get(string $id): ClientInterface
    {
        return $this->clients[$id] ?? throw new ContainerEntryNotFoundException(sprintf('The entry "%s" was not found in the container.', $id));
    }

    /**
     * @see Psr\Container\ContainerInterface
     */
    public function has(string $id): bool
    {
        return array_key_exists($id, $this->clients);
    }
}

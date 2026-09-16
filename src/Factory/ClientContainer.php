<?php

namespace OneToMany\PdfPack\Factory;

use OneToMany\PdfPack\Contract\Client\ClientInterface;
use OneToMany\PdfPack\Contract\Enum\Vendor;
use OneToMany\PdfPack\Exception\InvalidArgumentException;
use OneToMany\PdfPack\Factory\Exception\ContainerEntryNotFoundException;
use Psr\Container\ContainerInterface;

use function array_key_exists;
use function sprintf;
use function strtolower;
use function trim;

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
    ) {
        foreach ($clients as $client) {
            $this->addClient($client);
        }
    }

    public function addClient(ClientInterface $client): static
    {
        $vendor = $client::getVendor();
        $vendor = $vendor instanceof Vendor ? $vendor->getValue() : strtolower(trim($vendor));

        if ('' === $vendor) {
            throw new InvalidArgumentException('The client vendor cannot be empty.');
        }

        $this->clients[$vendor] = $client;

        return $this;
    }

    /**
     * @see Psr\Container\ContainerInterface
     *
     * @throws ContainerEntryNotFoundException when an entry was not found in the container
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

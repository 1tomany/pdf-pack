<?php

namespace OneToMany\PdfPack\Resource;

use OneToMany\PdfPack\Contract\Bridge\ProviderInterface;
use OneToMany\PdfPack\Exception\DomainException;

use function sprintf;

final readonly class Registry
{
    /**
     * @var array<non-empty-lowercase-string, ProviderInterface>
     */
    private array $providers;

    /**
     * @param iterable<ProviderInterface> $providers
     *
     * @throws DomainException when a provider is already registered
     */
    public function __construct(iterable $providers)
    {
        $indexedProviders = [];

        foreach ($providers as $provider) {
            $name = DomainException::validateProvider($provider::getProvider());

            if (isset($indexedProviders[$name])) {
                throw new DomainException(sprintf('The "%s" provider is already registered.', $name));
            }

            $indexedProviders[$name] = $provider;
        }

        $this->providers = $indexedProviders;
    }

    /**
     * @throws DomainException when a provider is not registered
     */
    public function get(string $provider): ProviderInterface
    {
        $provider = DomainException::validateProvider($provider);

        return $this->providers[$provider]
            ?? throw new DomainException(sprintf('The "%s" provider is not registered.', $provider));
    }
}

<?php

namespace OneToMany\PdfPack\Resource;

use OneToMany\PdfPack\Contract\Bridge\ProviderInterface;
use OneToMany\PdfPack\Exception\DomainException;
use OneToMany\PdfPack\Vendor;

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
            $vendor = $provider::getVendor()->getValue();

            if (isset($indexedProviders[$vendor])) {
                throw new DomainException(sprintf('The "%s" provider is already registered.', $vendor));
            }

            $indexedProviders[$vendor] = $provider;
        }

        $this->providers = $indexedProviders;
    }

    /**
     * @throws DomainException when a provider is not registered
     */
    public function get(Vendor $vendor): ProviderInterface
    {
        return $this->providers[$vendor->getValue()]
            ?? throw new DomainException(sprintf('The "%s" provider is not registered.', $vendor->getValue()));
    }
}

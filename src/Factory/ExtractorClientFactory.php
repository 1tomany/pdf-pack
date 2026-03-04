<?php

namespace OneToMany\PdfPack\Factory;

use OneToMany\PdfPack\Contract\Client\ExtractorClientInterface;
use OneToMany\PdfPack\Factory\Exception\CreatingExtractorClientFailedServiceNotFoundException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

final readonly class ExtractorClientFactory
{
    public function __construct(private ContainerInterface $container)
    {
    }

    public function create(string $id): ExtractorClientInterface
    {
        try {
            $service = $this->container->get($id);

            if (!$service instanceof ExtractorClientInterface) {
                throw new CreatingExtractorClientFailedServiceNotFoundException($id);
            }
        } catch (ContainerExceptionInterface $e) {
            throw new CreatingExtractorClientFailedServiceNotFoundException($id, $e);
        }

        return $service;
    }
}

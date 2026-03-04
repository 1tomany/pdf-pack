<?php

namespace OneToMany\PdfPack\Factory\Exception;

use OneToMany\PdfPack\Exception\InvalidArgumentException;

use function sprintf;

final class CreatingExtractorClientFailedServiceNotFoundException extends InvalidArgumentException
{
    public function __construct(string $service, ?\Throwable $previous = null)
    {
        parent::__construct(sprintf('Creating a client for the extractor service "%s" failed because a service with that tag could not be found.', $service), previous: $previous);
    }
}

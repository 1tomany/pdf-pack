<?php

namespace OneToMany\PdfPack\Factory\Exception;

use OneToMany\PdfPack\Exception\InvalidArgumentException;

use function sprintf;

final class CreatingClientFailedServiceNotFoundException extends InvalidArgumentException
{
    public function __construct(string $service, ?\Throwable $previous = null)
    {
        parent::__construct(sprintf('Creating a "%s" client failed because a service is not registered for it.', $service), previous: $previous);
    }
}

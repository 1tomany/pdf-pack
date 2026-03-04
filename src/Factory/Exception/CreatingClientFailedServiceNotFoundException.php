<?php

namespace OneToMany\PdfPack\Factory\Exception;

use OneToMany\PdfPack\Exception\InvalidArgumentException;

use function sprintf;

final class CreatingClientFailedServiceNotFoundException extends InvalidArgumentException
{
    public function __construct(string $service, ?\Throwable $previous = null)
    {
        parent::__construct(sprintf('Creating a "%s" client failed because it does not have a service registered.', $service), previous: $previous);
    }
}

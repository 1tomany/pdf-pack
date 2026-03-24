<?php

namespace OneToMany\PdfPack\Factory\Exception;

use OneToMany\PdfPack\Exception\InvalidArgumentException;
use Psr\Container\NotFoundExceptionInterface;

class ContainerEntryNotFoundException extends InvalidArgumentException implements NotFoundExceptionInterface
{
}

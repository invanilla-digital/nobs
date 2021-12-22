<?php

declare(strict_types=1);

namespace Invanilla\Nobs\Container\Exception;

use OutOfRangeException;
use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends OutOfRangeException implements NotFoundExceptionInterface
{

}

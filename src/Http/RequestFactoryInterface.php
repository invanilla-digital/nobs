<?php

declare(strict_types=1);

namespace Invanilla\Nobs\Http;

use Psr\Http\Message\RequestInterface;

interface RequestFactoryInterface
{
    public function makeFromGlobals(): RequestInterface;
}


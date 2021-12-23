<?php

declare(strict_types=1);

namespace Invanilla\Nobs\Application\Kernel;

use Invanilla\Nobs\Application\Kernel\Event\RequestReceived;
use Invanilla\Nobs\Http\RequestFactoryInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

class HttpKernel implements KernelInterface
{
    public function __construct(
        private readonly RequestFactoryInterface $requestFactory,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function start(): void
    {
        $this->eventDispatcher->dispatch(
            new RequestReceived(
                $this->requestFactory->makeFromGlobals()
            )
        );
    }
}

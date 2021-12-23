<?php

declare(strict_types=1);

namespace Invanilla\Nobs\Application\Kernel\Event;

use Psr\EventDispatcher\StoppableEventInterface;
use Psr\Http\Message\RequestInterface;

class RequestReceived implements StoppableEventInterface
{
    private bool $isPropagationStopped = false;

    public function __construct(
        private readonly RequestInterface $request
    ) {
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    public function stopPropagation(): void
    {
        $this->isPropagationStopped = true;
    }

    public function isPropagationStopped(): bool
    {
        return $this->isPropagationStopped;
    }
}

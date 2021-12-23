<?php

declare(strict_types=1);

namespace Invanilla\Nobs\Event;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;

class EventDispatcher implements EventDispatcherInterface
{
    public function __construct(
        private readonly ListenerProviderInterface $listeners
    ) {
    }

    public function dispatch(object $event): object
    {
        if (!$this->shouldDispatch($event)) {
            return $event;
        }

        foreach ($this->listeners->getListenersForEvent($event) as $listener) {
            $listener($event);
        }

        return $event;
    }

    private function shouldDispatch(object $event): bool
    {
        if (!$event instanceof StoppableEventInterface) {
            return true;
        }

        return !$event->isPropagationStopped();
    }
}

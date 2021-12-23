<?php

declare(strict_types=1);

namespace Invanilla\Nobs\Event;

use Psr\EventDispatcher\ListenerProviderInterface;

class ListenerProvider implements ListenerProviderInterface
{
    /**
     * @var array<ListenerInterface>
     */
    private array $listeners = [];

    public function getListenersForEvent(object $event): iterable
    {
        return $this->listeners[get_class($event)] ?? [];
    }

    public function addListener(string $eventClass, ListenerInterface $listener): void
    {
        $this->listeners[$eventClass][] = $listener;
    }

    public function removeAllListeners(string $eventClass): void
    {
        unset($this->listeners[$eventClass]);
    }
}

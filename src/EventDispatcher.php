<?php

namespace Acme\EventDispatcher;

use Acme\EventDispatcher\Exception\NoListenerException;
use Acme\EventDispatcher\Interface\EventListenerInterface;

class EventDispatcher
{
    private array $listeners = [];

    public function addListener(string $eventName, callable|EventListenerInterface $listener): void
    {
        $this->listeners[$eventName][] = $listener;
    }

    public function dispatch(object $event, ?string $eventName = null): object
    {
        $eventName ??= $event::class;

        if (!array_key_exists($eventName, $this->listeners)) {
            throw new NoListenerException($eventName);
        }

        $listeners = \array_unique($this->listeners[$eventName]);
        foreach ($listeners as $listener) {
            $listener instanceof EventListenerInterface
                ? $listener->handle($event)
                : $listener($event);
        }

        return $event;
    }
}

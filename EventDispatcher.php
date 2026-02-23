<?php

class EventDispatcher
{
    private array $listeners = [];

    public function addListener(string $eventName, callable $listener): void
    {
        $this->listeners[$eventName][] = $listener;
    }

    public function dispatch(object $event, ?string $eventName = null): object
    {
        $eventName ??= $event::class;

        $listeners = \array_unique($this->listeners[$eventName]);
        foreach ($listeners as $listener) {
            $listener($event);
        }

        return $event;
    }
}

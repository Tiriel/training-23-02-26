<?php

namespace Acme\EventDispatcher\Interface;

interface EventListenerInterface
{
    public function handle(object $event): void;
}

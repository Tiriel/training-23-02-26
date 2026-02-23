<?php

namespace Acme\EventDispatcher\Exception;

class NoListenerException extends \RuntimeException
{
    public function __construct(string $eventName = "")
    {
        parent::__construct(sprintf("No listener registered for event \"%s\"", $eventName));
    }
}

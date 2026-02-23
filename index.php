<?php

require_once __DIR__ . '/NoListenerException.php';
require_once __DIR__ . '/EventDispatcher.php';
require_once __DIR__ . '/EventListenerInterface.php';

class Listener implements EventListenerInterface
{
    public function handle(object $event): void
    {
        echo 'Foo event called : ' . $event->foo . \PHP_EOL;
    }
}

$dispatcher = new EventDispatcher();
$dispatcher->addListener('foo', new Listener());

try {
    $dispatcher->dispatch(new class {
        public string $foo = 'Hello World';
    }, 'bar');
} catch (NoListenerException $e) {
    echo "Exception : " . $e->getMessage() . \PHP_EOL;
}

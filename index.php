<?php

require_once __DIR__ . '/EventDispatcher.php';
require_once __DIR__ . '/EventListenerInterface.php';

$dispatcher = new EventDispatcher();
$dispatcher->addListener('foo', new class implements EventListenerInterface {
    public function handle(object $event): void
    {
        echo 'Foo event called : ' . $event->foo . \PHP_EOL;
    }
});

$dispatcher->dispatch(new class {
    public string $foo = 'Hello World';
}, 'foo');

<?php

require_once __DIR__ . '/EventDispatcher.php';

$dispatcher = new EventDispatcher();
$dispatcher->addListener('foo', function (object $event) {
    echo 'Foo event called : ' . $event->foo . \PHP_EOL;
});

$dispatcher->dispatch(new class {
    public string $foo = 'Hello World';
}, 'foo');

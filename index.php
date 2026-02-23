<?php

use Acme\EventDispatcher\EventDispatcher;
use Acme\EventDispatcher\Exception\NoListenerException;
use Acme\EventDispatcher\Interface\EventListenerInterface;

spl_autoload_register(function ($class) {
    $class = substr($class, strlen('Acme\\EventDispatcher\\'));
    $class = strtr($class, '\\', '/');

    require_once sprintf('%s/src/%s.php', __DIR__, $class);
});

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
    }, 'foo');
} catch (NoListenerException $e) {
    echo "Exception : " . $e->getMessage() . \PHP_EOL;
}

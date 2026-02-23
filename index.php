<?php

use Acme\EventDispatcher\EventDispatcher;
use Acme\EventDispatcher\Exception\NoListenerException;
use Acme\EventDispatcher\Interface\EventListenerInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once __DIR__ . '/vendor/autoload.php';

$twig = new Environment(
    new FilesystemLoader(__DIR__.'/templates')
);

class Listener implements EventListenerInterface
{
    public function __construct(private readonly Environment $twig){}

    public function handle(object $event): void
    {
        echo $this->twig->render('index.html.twig', ['event' => $event->foo]);
    }
}

$dispatcher = new EventDispatcher();
$dispatcher->addListener('foo', new Listener($twig));

try {
    $dispatcher->dispatch(new class {
        public string $foo = 'Hello World';
    }, 'foo');
} catch (NoListenerException $e) {
    echo "Exception : " . $e->getMessage() . \PHP_EOL;
}

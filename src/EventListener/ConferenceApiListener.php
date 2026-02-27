<?php

namespace App\EventListener;

use App\Entity\Conference;
use App\Search\ApiConferencePersister;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ViewEvent;

#[AsEventListener]
final class ConferenceApiListener
{
    public function __construct(
        private readonly ApiConferencePersister $persister,
    ) {}

    public function __invoke(ViewEvent $event): void
    {
        $request = $event->getRequest();
        if ('app_conference_search' !== $request->attributes->get('_route')) {
            return;
        }

        $result = $event->getControllerResult();
        $result['conferences'] = $this->persister->parseApiResults($result['conferences']);

        $event->setControllerResult($result);
    }
}

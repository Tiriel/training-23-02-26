<?php

namespace App\Search;

use App\Search\ConferenceSearchInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When('dev')]
#[AsDecorator(ConferenceSearchInterface::class, priority: 10)]
class TraceableConferenceSearch implements ConferenceSearchInterface
{
    public function __construct(
        private readonly ConferenceSearchInterface $inner,
        private readonly LoggerInterface $logger
    ) {}

    public function searchByName(?string $name = null): array
    {
        $this->logger->info('Searched for conferences with name : '.$name.'"');

        return $this->inner->searchByName($name);
    }
}

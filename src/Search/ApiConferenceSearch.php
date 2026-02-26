<?php

namespace App\Search;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

class ApiConferenceSearch implements ConferenceSearchInterface
{
    public function __construct(
        #[Autowire(env: 'CONFERENCES_API_KEY')]
        private readonly string $apiKey,
    ) {}

    public function searchByName(?string $name = null): array
    {
        // TODO: Implement searchByName() method.
    }
}

<?php

namespace App\Search;

use App\Search\Interface\ConferenceSearchInterface;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsAlias]
class ApiConferenceSearch implements ConferenceSearchInterface
{
    public function __construct(
        #[Target('conf.client')]
        private readonly HttpClientInterface $client,
    ) {}

    public function searchByName(?string $name = null): array
    {
        return $this->client->request(
            Request::METHOD_GET,
            '/events',
            ['query' => ['name' => $name],]
        )->toArray();
    }
}

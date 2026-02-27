<?php

namespace App\Search;

use App\Search\Interface\ConferenceSearchInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;

#[AsAlias]
class UnifiedConferenceSearch implements ConferenceSearchInterface
{
    public function __construct(
        /** @var ContainerInterface<string, ConferenceSearchInterface> $searches */
        #[AutowireLocator([
            'database' => DatabaseConferenceSearch::class,
            'api' => ApiConferenceSearch::class,
        ])]
        private readonly ContainerInterface $searches
    ) {}

    public function searchByName(?string $name = null): array
    {
        $dbConfs = $this->searches->get('database')->searchByName($name);
        $apiConfs = [];

        if (\is_string($name)) {
            $apiConfs = $this->searches->get('api')->searchByName($name);
        }

        return \array_unique(\array_merge($dbConfs, $apiConfs), SORT_REGULAR);
    }
}

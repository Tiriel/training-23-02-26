<?php

namespace App\Search;

use App\Repository\ConferenceRepository;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;

#[AsAlias]
class DatabaseConferenceSearch implements ConferenceSearchInterface
{
    public function __construct(
        private readonly ConferenceRepository $repository,
    ) {}

    public function searchByName(?string $name = null): array
    {
        if (null === $name) {
            return $this->repository->findAll();
        }

        return $this->repository->findLikeName($name);
    }
}

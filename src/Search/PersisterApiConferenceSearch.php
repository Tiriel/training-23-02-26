<?php

namespace App\Search;

use App\Entity\Conference;
use App\Entity\Organization;
use App\Repository\ConferenceRepository;
use App\Repository\OrganizationRepository;
use App\Search\Interface\ConferenceSearchInterface;
use App\Search\Transformer\ApiConferenceTransformer;
use App\Search\Transformer\ApiOrganizationTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;

#[AsDecorator(ApiConferenceSearch::class)]
class PersisterApiConferenceSearch implements ConferenceSearchInterface
{
    public function __construct(
        private readonly ConferenceSearchInterface $inner,
        private readonly EntityManagerInterface $manager,
        private readonly ConferenceRepository $conferenceRepository,
        private readonly OrganizationRepository $organizationRepository,
        private readonly ApiConferenceTransformer $confTransformer,
        private readonly ApiOrganizationTransformer $orgTransformer,
    ) {}

    public function searchByName(?string $name = null): array
    {
        return \array_map(function (array $apiConf) {
            $conference = $this->getOrCreateConference($apiConf);

            $this->addOrganizations($apiConf['organizations'], $conference);

            $this->manager->flush();

            return $conference;
        }, $this->inner->searchByName($name));
    }

    private function getOrCreateConference(array $apiConf): Conference
    {
        $conference = $this->conferenceRepository->findOneBy([
            'name' => $apiConf['name'],
            'startAt' => new \DateTimeImmutable($apiConf['startDate'])
        ]);

        if (null === $conference) {
            $conference = $this->confTransformer->transform($apiConf);
            $this->manager->persist($conference);
        }

        return $conference;
    }

    private function addOrganizations(array $apiOrgs, Conference $conference): void
    {
        foreach ($apiOrgs as $apiOrg) {
            $organization = $this->organizationRepository->findOneBy(['name' => $apiOrg])
                ?? $this->orgTransformer->transform($apiOrg);

            $conference->addOrganization($organization);
            $organization->addConference($conference);
        }

    }
}

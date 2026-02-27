<?php

namespace App\Search;

use App\Entity\Conference;
use App\Entity\Organization;
use App\Entity\User;
use App\Repository\ConferenceRepository;
use App\Repository\OrganizationRepository;
use App\Search\Transformer\ApiConferenceTransformer;
use App\Search\Transformer\ApiOrganizationTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ApiConferencePersister
{
    public function __construct(
        private readonly EntityManagerInterface $manager,
        private readonly ConferenceRepository $conferenceRepository,
        private readonly OrganizationRepository $organizationRepository,
        private readonly ApiConferenceTransformer $conferenceTransformer,
        private readonly ApiOrganizationTransformer $organizationTransformer,
        private readonly AuthorizationCheckerInterface $checker,
        private readonly TokenStorageInterface $tokenStorage,
    ) {}

    public function parseApiResults(array $apiConfs): array
    {
        $conferences = [];

        foreach ($apiConfs as $apiConf) {
            $conferences[] = $this->persistConference($apiConf);
        }

        if ($this->checker->isGranted('ROLE_ORGANIZER') || $this->checker->isGranted('ROLE_WEBSITE')) {
            $this->manager->flush();
        }

        return $conferences;
    }

    public function persistConference(array $apiConf): Conference
    {
        $conference = $this->searchConference($apiConf);

        if (null === $conference) {
            $conference = $this->conferenceTransformer->transform($apiConf);
            $this->manager->persist($conference);
        }

        $user = $this->tokenStorage->getToken()->getUser();

        if ($user instanceof User) {
            $conference->setCreatedBy($user);
        }

        foreach ($this->parseOrganizations($apiConf['organizations']) as $organization) {
            /** @var Organization $organization */
            $this->manager->persist($organization);
            $conference->addOrganization($organization);
            $organization->addConference($conference);
        }

        return $conference;
    }

    private function searchConference(array $apiConf): ?Conference
    {
        return $this->conferenceRepository->findOneBy([
            'name' => $apiConf['name'],
            'startAt' => new \DateTimeImmutable($apiConf['startDate']),
        ]);
    }

    /**
     * @return Organization[]
     */
    private function parseOrganizations(array $apiOrgs): iterable
    {
        foreach ($apiOrgs as $apiOrg) {
            yield $this->organizationRepository->findOneBy(['name' => $apiOrg['name']])
                ?? $this->organizationTransformer->transform($apiOrg);
        }
    }
}

<?php

namespace App\EventListener;

use App\Entity\Conference;
use App\Entity\Organization;
use App\Repository\ConferenceRepository;
use App\Repository\OrganizationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ViewEvent;

final class ConferenceApiListener
{
    public function __construct(
        private readonly EntityManagerInterface $manager,
        private readonly ConferenceRepository $conferenceRepository,
        private readonly OrganizationRepository $organizationRepository,
    ) {}

    #[AsEventListener]
    public function onViewEvent(ViewEvent $event): void
    {
        $request = $event->getRequest();
        if ('app_conference_search' !== $request->attributes->get('_route')) {
            return;
        }

        $result = $event->getControllerResult();
        $result['conferences'] = \array_map(function (array $apiConf) {
            $conference = $this->searchConference($apiConf);

            if (null === $conference) {
                $conference = $this->saveConference($apiConf);
            }

            foreach ($this->parseOrganizations($apiConf['organizations']) as $organization) {
                /** @var Organization $organization */
                $conference->addOrganization($organization);
                $organization->addConference($conference);
            }

            $this->manager->flush();

            return $conference;
        }, $result['conferences']);

        $event->setControllerResult($result);
    }

    private function searchConference(array $apiConf): ?Conference
    {
        return $this->conferenceRepository->findOneBy([
            'name' => $apiConf['name'],
            'startAt' => new \DateTimeImmutable($apiConf['startDate']),
        ]);
    }

    private function saveConference(array $apiConf): Conference
    {
        $conf = (new Conference())
            ->setName($apiConf['name'])
            ->setDescription($apiConf['description'])
            ->setPrerequisites($apiConf['prerequisites'])
            ->setAccessible($apiConf['accessible'])
            ->setStartAt(new \DateTimeImmutable($apiConf['startDate']))
            ->setEndAt(new \DateTimeImmutable($apiConf['endDate']));

        $this->manager->persist($conf);

        return $conf;
    }

    /**
     * @return Organization[]
     */
    private function parseOrganizations(array $apiOrgs): iterable
    {
        foreach ($apiOrgs as $apiOrg) {
            $org = $this->organizationRepository->findOneBy(['name' => $apiOrg['name']])
                ?? (new Organization())
                    ->setName($apiOrg['name'])
                    ->setPresentation($apiOrg['presentation'])
                    ->setCreatedAt(new \DateTimeImmutable($apiOrg['createdAt'] ?? 'now'));

            $this->manager->persist($org);

            yield $org;
        }
    }
}

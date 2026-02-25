<?php

namespace App\Controller;

use App\Entity\Conference;
use App\Repository\ConferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/conference')]
class ConferenceController extends AbstractController
{
    #[Route('', name: 'app_conference_list')]
    public function list(ConferenceRepository $repository): Response
    {
        $conferences = $repository->findAll();
        $conferences= \array_map(fn(Conference $conference) => [
            'id' => $conference->getId(),
            'name' => $conference->getName(),
        ], $conferences);

        return $this->json($conferences);
    }

    #[Route('/{id}', name: 'app_conference_show', requirements: ['id' => Requirement::POSITIVE_INT])]
    public function show(Conference $conference): Response
    {
        return $this->json([
            'id' => $conference->getId(),
            'name' => $conference->getName(),
        ]);
    }

    #[Route(
        '/{name}/{start}/{end}',
        name: 'app_conference_new',
        requirements: ['name' => Requirement::CATCH_ALL, 'start' => Requirement::DATE_YMD, 'end' => Requirement::DATE_YMD]
    )]
    public function newConference(string $name, string $start, string $end, EntityManagerInterface $manager): Response
    {
        $conference = (new Conference())
            ->setName($name)
            ->setDescription('Some generic description')
            ->setAccessible(true)
            ->setStartAt(new \DateTimeImmutable($start))
            ->setEndAt(new \DateTimeImmutable($end))
        ;

        $manager->persist($conference);
        $manager->flush();

        return new Response('Conference created');
    }
}

<?php

namespace App\Controller;

use App\Entity\Conference;
use App\Form\ConferenceType;
use App\Search\ConferenceSearchInterface;
use App\Search\DatabaseConferenceSearch;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/conference')]
class ConferenceController extends AbstractController
{
    #[Route('', name: 'app_conference_list', methods: ['GET'])]
    public function list(
        Request $request,
        /** #[Autowire(service: DatabaseConferenceSearch::class)] */
        ConferenceSearchInterface $search
    ): Response {
        $name = $request->query->get('name');

        return $this->render('conference/list.html.twig', [
            'conferences' => $search->searchByName($name),
        ]);
    }

    #[Route('/{id}', name: 'app_conference_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Conference $conference): Response
    {
        return $this->render('conference/show.html.twig', [
            'conference' => $conference,
        ]);
    }

    #[Route('/new', name: 'app_conference_new', methods: ['GET', 'POST'])]
    public function newConference(Request $request, EntityManagerInterface $manager): Response
    {
        $conference = new Conference();
        $form = $this->createForm(ConferenceType::class, $conference);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($conference);
            $manager->flush();

            return $this->redirectToRoute('app_conference_show', ['id' => $conference->getId()]);
        }

        return $this->render('conference/new.html.twig', [
            'form' => $form,
        ]);
    }
}

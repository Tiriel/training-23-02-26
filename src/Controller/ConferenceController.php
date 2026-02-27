<?php

namespace App\Controller;

use App\Entity\Conference;
use App\Form\ConferenceType;
use App\Search\Interface\ConferenceSearchInterface;
use App\Search\DatabaseConferenceSearch;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/conference')]
class ConferenceController extends AbstractController
{
    #[Route('', name: 'app_conference_list', methods: ['GET'])]
    public function list(
        Request $request,
        #[Autowire(service: DatabaseConferenceSearch::class)]
        ConferenceSearchInterface $search
    ): Response {
        $name = $request->query->get('name');

        return $this->render('conference/list.html.twig', [
            'conferences' => $search->searchByName($name),
        ]);
    }

    #[Route('/conference/search', name: 'app_conference_search', methods: ['GET'])]
    #[Template('conference/list.html.twig')]
    public function search(Request $request, ConferenceSearchInterface $search): array
    {
        return ['conferences' => $search->searchByName($request->query->get('name'))];
    }

    #[Route('/{id}', name: 'app_conference_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Conference $conference): Response
    {
        return $this->render('conference/show.html.twig', [
            'conference' => $conference,
        ]);
    }

    #[IsGranted(new Expression('is_granted("ROLE_ORGANIZER") or is_granted("ROLE_WEBSITE")'))]
    #[Route('/new', name: 'app_conference_new', methods: ['GET', 'POST'])]
    #[Route('/{id}/edit', name: 'app_conference_edit', methods: ['GET', 'POST'])]
    public function newConference(?Conference $conference, Request $request, EntityManagerInterface $manager): Response
    {
        //if (!$this->isGranted('ROLE_ORGANIZER') && !$this->isGranted('ROLE_WEBSITE')) {
        //    throw $this->createAccessDeniedException('Only Organizers or Website users are allowed here.');
        //}

        $conference ??= new Conference();
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

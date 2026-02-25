<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $name = $request->query->getString('name', 'World');

        return $this->render('main/index.html.twig', ['name' => $name]);
    }

    #[Route('/contact', name: 'app_main_contact', methods: ['GET'])]
    public function contact(Request $request): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            dump($form->getData());

            return $this->redirectToRoute('app_main_contact');
        }

        return $this->render('main/contact.html.twig', [
            'form' => $form,
        ]);
    }

    //#[Route('/contact', name: 'app_main_contact', methods: ['GET'])]
    //#[Template('main/contact.html.twig')]
    //public function contact(): void {}
}

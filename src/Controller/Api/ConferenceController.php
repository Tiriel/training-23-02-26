<?php

namespace App\Controller\Api;

use App\Repository\ConferenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

final class ConferenceController extends AbstractController
{
    #[Route('/api/conference', name: 'app_api_conference_get')]
    public function getConferences(Request $request, ConferenceRepository $repository): JsonResponse
    {
        $page = $request->query->get('page', 1);
        $limit = 10;

        return $this->json(
            $repository->findBy([], [], $limit, ($page - 1) * $limit),
            context: [
                AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => fn(object $o) => ['id' => $o->getId()],
            ],
        );
    }
}

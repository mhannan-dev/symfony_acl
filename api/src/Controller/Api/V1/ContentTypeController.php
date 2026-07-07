<?php

namespace App\Controller\Api\V1;

use App\Entity\ContentType;
use App\Repository\ContentTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/v1/content-types')]
class ContentTypeController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
    ) {
    }

    #[Route('', name: 'api_v1_content_types_list', methods: ['GET'])]
    #[IsGranted('view_content_type')]
    public function index(ContentTypeRepository $repo): JsonResponse
    {
        $items = $repo->findAll();

        return $this->json([
            'contentTypes' => $this->serializer->normalize($items, null, ['groups' => ['content_type:read']]),
        ]);
    }
}

<?php

namespace App\Controller\Api;

use App\Entity\ContentType;
use App\Repository\ContentTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/v1/content-types')]
class ContentTypeController extends AbstractController
{
    #[Route('', name: 'api_content_types_list', methods: ['GET'])]
    #[IsGranted('view_content_type')]
    public function index(ContentTypeRepository $repo): JsonResponse
    {
        $items = $repo->findAll();
        $data = array_map(fn(ContentType $ct) => [
            'id' => $ct->getId(),
            'appLabel' => $ct->getAppLabel(),
            'model' => $ct->getModel(),
            'permissions' => array_map(fn($p) => $p->getId(), $ct->getPermissions()->toArray()),
        ], $items);

        return $this->json(['contentTypes' => $data]);
    }
}

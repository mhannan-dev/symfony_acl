<?php

namespace App\Controller\Api;

use App\Entity\ContentType;
use App\Repository\ContentTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/content-types')]
class ContentTypeController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    #[Route('', name: 'api_content_types_list', methods: ['GET'])]
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

    #[Route('/new', name: 'api_content_types_new_form', methods: ['GET'])]
    public function new(): JsonResponse
    {
        return $this->json(['contentType' => null]);
    }

    #[Route('/save', name: 'api_content_types_save', methods: ['POST'])]
    public function save(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $ct = $data['id'] ? $this->em->getRepository(ContentType::class)->find($data['id']) : new ContentType();
        $ct->setAppLabel($data['appLabel']);
        $ct->setModel($data['model']);

        $this->em->persist($ct);
        $this->em->flush();

        return $this->json(['contentType' => ['id' => $ct->getId(), 'appLabel' => $ct->getAppLabel(), 'model' => $ct->getModel()]]);
    }

    #[Route('/{id}/edit', name: 'api_content_types_edit_form', methods: ['GET'])]
    public function edit(ContentType $contentType): JsonResponse
    {
        return $this->json([
            'contentType' => [
                'id' => $contentType->getId(),
                'appLabel' => $contentType->getAppLabel(),
                'model' => $contentType->getModel(),
            ],
        ]);
    }

    #[Route('/{id}/delete', name: 'api_content_types_delete', methods: ['DELETE'])]
    public function delete(ContentType $contentType): JsonResponse
    {
        $this->em->remove($contentType);
        $this->em->flush();

        return $this->json(['message' => 'Content type deleted.']);
    }
}

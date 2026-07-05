<?php

namespace App\Controller\Api;

use App\Entity\Permission;
use App\Repository\ContentTypeRepository;
use App\Repository\PermissionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1/permissions')]
class PermissionController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    #[Route('', name: 'api_permissions_list', methods: ['GET'])]
    public function index(PermissionRepository $repo): JsonResponse
    {
        $permissions = $repo->findAll();
        $data = array_map(fn(Permission $perm) => [
            'id' => $perm->getId(),
            'name' => $perm->getName(),
            'codename' => $perm->getCodename(),
            'contentType' => $perm->getContentType() ? [
                'id' => $perm->getContentType()->getId(),
                'appLabel' => $perm->getContentType()->getAppLabel(),
                'model' => $perm->getContentType()->getModel(),
            ] : null,
        ], $permissions);

        return $this->json(['permissions' => $data]);
    }

    #[Route('/new', name: 'api_permissions_new_form', methods: ['GET'])]
    public function new(ContentTypeRepository $ctRepo): JsonResponse
    {
        $contentTypes = array_map(fn($ct) => ['id' => $ct->getId(), 'appLabel' => $ct->getAppLabel(), 'model' => $ct->getModel()], $ctRepo->findAll());

        return $this->json(['permission' => null, 'contentTypes' => $contentTypes]);
    }

    #[Route('/save', name: 'api_permissions_save', methods: ['POST'])]
    public function save(Request $request, ContentTypeRepository $ctRepo): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $permission = $data['id'] ? $this->em->getRepository(Permission::class)->find($data['id']) : new Permission();
        $permission->setName($data['name']);
        $permission->setCodename($data['codename']);

        if (!empty($data['contentTypeId'])) {
            $ct = $ctRepo->find($data['contentTypeId']);
            $permission->setContentType($ct);
        }

        $this->em->persist($permission);
        $this->em->flush();

        return $this->json(['permission' => ['id' => $permission->getId(), 'name' => $permission->getName(), 'codename' => $permission->getCodename()]]);
    }

    #[Route('/{id}/edit', name: 'api_permissions_edit_form', methods: ['GET'])]
    public function edit(Permission $permission, ContentTypeRepository $ctRepo): JsonResponse
    {
        $contentTypes = array_map(fn($ct) => ['id' => $ct->getId(), 'appLabel' => $ct->getAppLabel(), 'model' => $ct->getModel()], $ctRepo->findAll());

        return $this->json([
            'permission' => [
                'id' => $permission->getId(),
                'name' => $permission->getName(),
                'codename' => $permission->getCodename(),
                'contentType' => $permission->getContentType() ? ['id' => $permission->getContentType()->getId()] : null,
            ],
            'contentTypes' => $contentTypes,
        ]);
    }

    #[Route('/{id}/delete', name: 'api_permissions_delete', methods: ['DELETE'])]
    public function delete(Permission $permission): JsonResponse
    {
        $this->em->remove($permission);
        $this->em->flush();

        return $this->json(['message' => 'Permission deleted.']);
    }
}

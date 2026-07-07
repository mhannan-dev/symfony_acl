<?php
declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Entity\Permission;
use App\Repository\ContentTypeRepository;
use App\Repository\PermissionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/v1/permissions')]
class PermissionController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly SerializerInterface $serializer,
    ) {
    }

    #[Route('', name: 'api_v1_permissions_list', methods: ['GET'])]
    #[IsGranted('view_permission')]
    public function index(Request $request, PermissionRepository $repo): JsonResponse
    {
        $page = max(1, $request->query->getInt('page', 1));
        $perPage = min(100, max(1, $request->query->getInt('perPage', 10)));
        $search = $request->query->getString('search', '');

        $qb = $repo->createQueryBuilder('p')
            ->leftJoin('p.contentType', 'ct');

        if ($search) {
            $qb->where('p.name LIKE :search OR p.codename LIKE :search OR ct.appLabel LIKE :search OR ct.model LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }

        $total = (clone $qb)->select('COUNT(p.id)')->getQuery()->getSingleScalarResult();
        $permissions = $qb->select('p')
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage)
            ->getQuery()
            ->getResult();

        return $this->json([
            'permissions' => $this->serializer->normalize($permissions, null, ['groups' => ['permission:read']]),
            'pagination' => [
                'currentPage' => $page,
                'perPage' => $perPage,
                'total' => (int) $total,
                'lastPage' => (int) ceil($total / $perPage),
            ],
        ]);
    }

    #[Route('/new', name: 'api_v1_permissions_new_form', methods: ['GET'])]
    #[IsGranted('add_permission')]
    public function new(ContentTypeRepository $ctRepo): JsonResponse
    {
        $contentTypes = $ctRepo->findAll();

        return $this->json([
            'permission' => null,
            'contentTypes' => $this->serializer->normalize($contentTypes, null, ['groups' => ['content_type:read']]),
        ]);
    }

    #[Route('/save', name: 'api_v1_permissions_save', methods: ['POST'])]
    public function save(Request $request, ContentTypeRepository $ctRepo): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $this->denyAccessUnlessGranted(empty($data['id']) ? 'create' : 'edit', 'permission');

        $permission = $data['id'] ? $this->em->getRepository(Permission::class)->find($data['id']) : new Permission();
        $permission->setName($data['name']);
        $permission->setCodename($data['codename']);

        if (!empty($data['contentTypeId'])) {
            $ct = $ctRepo->find($data['contentTypeId']);
            $permission->setContentType($ct);
        }

        $this->em->persist($permission);
        $this->em->flush();

        return $this->json([
            'permission' => $this->serializer->normalize($permission, null, ['groups' => ['permission:brief']]),
        ]);
    }

    #[Route('/{id}/edit', name: 'api_v1_permissions_edit_form', methods: ['GET'])]
    #[IsGranted('change_permission')]
    public function edit(Permission $permission, ContentTypeRepository $ctRepo): JsonResponse
    {
        $contentTypes = $ctRepo->findAll();

        return $this->json([
            'permission' => $this->serializer->normalize($permission, null, ['groups' => ['permission:read']]),
            'contentTypes' => $this->serializer->normalize($contentTypes, null, ['groups' => ['content_type:read']]),
        ]);
    }

    #[Route('/{id}/delete', name: 'api_v1_permissions_delete', methods: ['DELETE'])]
    #[IsGranted('delete_permission')]
    public function delete(Permission $permission): JsonResponse
    {
        $this->em->remove($permission);
        $this->em->flush();

        return $this->json(['message' => 'Permission deleted.']);
    }
}

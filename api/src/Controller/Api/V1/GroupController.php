<?php

declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Controller\Api\ApiResponseTrait;
use App\Entity\Group;
use App\Entity\GroupPermission;
use App\Repository\GroupRepository;
use App\Repository\PermissionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/v1/groups')]
class GroupController extends AbstractController
{
    use ApiResponseTrait;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly SerializerInterface $serializer,
    ) {
    }

    #[Route('', name: 'api_v1_groups_list', methods: ['GET'])]
    #[IsGranted('view_group')]
    public function index(Request $request, GroupRepository $repo): JsonResponse
    {
        $page = max(1, $request->query->getInt('page', 1));
        $perPage = min(100, max(1, $request->query->getInt('perPage', 10)));
        $search = $request->query->getString('search', '');

        $qb = $repo->createQueryBuilder('g');

        if ($search) {
            $qb->where('g.name LIKE :search')
               ->setParameter('search', '%'.$search.'%');
        }

        $total = (clone $qb)->select('COUNT(g.id)')->getQuery()->getSingleScalarResult();
        $groups = $qb->select('g')
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage)
            ->getQuery()
            ->getResult();

        return $this->apiSuccess([
            'groups' => $this->serializer->normalize($groups, null, ['groups' => ['group:read']]),
            'pagination' => [
                'currentPage' => $page,
                'perPage' => $perPage,
                'total' => (int) $total,
                'lastPage' => (int) ceil($total / $perPage),
            ],
        ]);
    }

    #[Route('/new', name: 'api_v1_groups_new_form', methods: ['GET'])]
    #[IsGranted('add_group')]
    public function new(PermissionRepository $permRepo): JsonResponse
    {
        $permissions = $permRepo->findAll();

        return $this->apiSuccess([
            'group' => null,
            'permissions' => $this->serializer->normalize($permissions, null, ['groups' => ['permission:brief']]),
            'groupPermissionIds' => [],
        ]);
    }

    #[Route('/save', name: 'api_v1_groups_save', methods: ['POST'])]
    public function save(Request $request, PermissionRepository $permRepo): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $this->denyAccessUnlessGranted(empty($data['id']) ? 'create' : 'edit', 'group');

        if (empty($data['name'])) {
            return $this->apiValidationError('Group name is required');
        }

        $group = $data['id'] ? $this->em->getRepository(Group::class)->find($data['id']) : new Group();
        $group->setName($data['name']);

        foreach ($group->getGroupPermissions()->toArray() as $gp) {
            $this->em->remove($gp);
        }
        $group->getGroupPermissions()->clear();
        $this->em->flush();

        $selectedPermIds = $data['permissionIds'] ?? [];
        foreach ($selectedPermIds as $permId) {
            $perm = $permRepo->find($permId);
            if ($perm) {
                $gp = new GroupPermission();
                $gp->setGroup($group);
                $gp->setPermission($perm);
                $this->em->persist($gp);
                $group->addGroupPermission($gp);
            }
        }

        $this->em->persist($group);
        $this->em->flush();

        return $this->apiSuccess([
            'group' => $this->serializer->normalize($group, null, ['groups' => ['group:brief']]),
        ]);
    }

    #[Route('/{id}/edit', name: 'api_v1_groups_edit_form', methods: ['GET'])]
    #[IsGranted('change_group')]
    public function edit(Group $group, PermissionRepository $permRepo): JsonResponse
    {
        $permissions = $permRepo->findAll();
        $groupPermissionIds = array_map(static fn ($gp) => $gp->getPermission()->getId(), $group->getGroupPermissions()->toArray());

        return $this->apiSuccess([
            'group' => $this->serializer->normalize($group, null, ['groups' => ['group:read']]),
            'permissions' => $this->serializer->normalize($permissions, null, ['groups' => ['permission:brief']]),
            'groupPermissionIds' => $groupPermissionIds,
        ]);
    }

    #[Route('/{id}/toggle-status', name: 'api_v1_groups_toggle_status', methods: ['PATCH'])]
    #[IsGranted('change_group')]
    public function toggleStatus(Group $group): JsonResponse
    {
        $group->setStatus(!$group->isStatus());
        $this->em->flush();

        return $this->apiSuccess([
            'id' => $group->getId(),
            'status' => $group->isStatus(),
        ]);
    }

    #[Route('/{id}/delete', name: 'api_v1_groups_delete', methods: ['DELETE'])]
    #[IsGranted('delete_group')]
    public function delete(Group $group): JsonResponse
    {
        $this->em->remove($group);
        $this->em->flush();

        return $this->apiSuccess(['message' => 'Group deleted.']);
    }
}

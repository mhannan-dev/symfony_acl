<?php

namespace App\Controller\Api;

use App\Entity\Group;
use App\Entity\GroupPermission;
use App\Repository\GroupRepository;
use App\Repository\PermissionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1/groups')]
class GroupController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    #[Route('', name: 'api_groups_list', methods: ['GET'])]
    public function index(Request $request, GroupRepository $repo): JsonResponse
    {
        $page = max(1, $request->query->getInt('page', 1));
        $perPage = min(100, max(1, $request->query->getInt('perPage', 10)));
        $search = $request->query->getString('search', '');

        $qb = $repo->createQueryBuilder('g');

        if ($search) {
            $qb->where('g.name LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }

        $total = (clone $qb)->select('COUNT(g.id)')->getQuery()->getSingleScalarResult();
        $groups = $qb->select('g')
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage)
            ->getQuery()
            ->getResult();

        $data = array_map(fn(Group $group) => [
            'id' => $group->getId(),
            'name' => $group->getName(),
            'groupPermissions' => array_map(
                fn($gp) => $gp->getPermission()->getId(),
                $group->getGroupPermissions()->toArray()
            ),
        ], $groups);

        return $this->json([
            'groups' => $data,
            'pagination' => [
                'currentPage' => $page,
                'perPage' => $perPage,
                'total' => (int) $total,
                'lastPage' => (int) ceil($total / $perPage),
            ],
        ]);
    }

    #[Route('/new', name: 'api_groups_new_form', methods: ['GET'])]
    public function new(PermissionRepository $permRepo): JsonResponse
    {
        $permissions = array_map(fn($p) => ['id' => $p->getId(), 'name' => $p->getName(), 'codename' => $p->getCodename()], $permRepo->findAll());

        return $this->json(['group' => null, 'permissions' => $permissions, 'groupPermissionIds' => []]);
    }

    #[Route('/save', name: 'api_groups_save', methods: ['POST'])]
    public function save(Request $request, PermissionRepository $permRepo): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $group = $data['id'] ? $this->em->getRepository(Group::class)->find($data['id']) : new Group();
        $group->setName($data['name']);

        foreach ($group->getGroupPermissions()->toArray() as $gp) {
            $this->em->remove($gp);
        }
        $group->getGroupPermissions()->clear();

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

        return $this->json(['group' => ['id' => $group->getId(), 'name' => $group->getName()]]);
    }

    #[Route('/{id}/edit', name: 'api_groups_edit_form', methods: ['GET'])]
    public function edit(Group $group, PermissionRepository $permRepo): JsonResponse
    {
        $permissions = array_map(fn($p) => ['id' => $p->getId(), 'name' => $p->getName(), 'codename' => $p->getCodename()], $permRepo->findAll());
        $groupPermissionIds = array_map(fn($gp) => $gp->getPermission()->getId(), $group->getGroupPermissions()->toArray());

        return $this->json([
            'group' => ['id' => $group->getId(), 'name' => $group->getName()],
            'permissions' => $permissions,
            'groupPermissionIds' => $groupPermissionIds,
        ]);
    }

    #[Route('/{id}/delete', name: 'api_groups_delete', methods: ['DELETE'])]
    public function delete(Group $group): JsonResponse
    {
        $this->em->remove($group);
        $this->em->flush();

        return $this->json(['message' => 'Group deleted.']);
    }
}

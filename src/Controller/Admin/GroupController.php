<?php

namespace App\Controller\Admin;

use App\Entity\Group;
use App\Entity\GroupPermission;
use App\Repository\GroupRepository;
use App\Repository\PermissionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Rompetomp\InertiaBundle\Architecture\InertiaInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/groups')]
class GroupController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    #[Route('', name: 'app_admin_groups')]
    public function index(InertiaInterface $inertia, GroupRepository $repo)
    {
        return $inertia->render('Groups/Index', [
            'groups' => $repo->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_groups_new')]
    public function new(InertiaInterface $inertia, PermissionRepository $permRepo)
    {
        return $inertia->render('Groups/Form', [
            'group' => null,
            'permissions' => $permRepo->findAll(),
            'groupPermissionIds' => [],
        ]);
    }

    #[Route('/save', name: 'app_admin_groups_save', methods: ['POST'])]
    public function save(Request $request, PermissionRepository $permRepo)
    {
        $data = $request->request->all();

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

        return $this->redirectToRoute('app_admin_groups');
    }

    #[Route('/{id}/edit', name: 'app_admin_groups_edit')]
    public function edit(InertiaInterface $inertia, Group $group, PermissionRepository $permRepo)
    {
        return $inertia->render('Groups/Form', [
            'group' => $group,
            'permissions' => $permRepo->findAll(),
            'groupPermissionIds' => array_map(
                fn ($gp) => $gp->getPermission()->getId(),
                $group->getGroupPermissions()->toArray()
            ),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_admin_groups_delete', methods: ['POST'])]
    public function delete(Group $group)
    {
        $this->em->remove($group);
        $this->em->flush();
        return $this->redirectToRoute('app_admin_groups');
    }
}

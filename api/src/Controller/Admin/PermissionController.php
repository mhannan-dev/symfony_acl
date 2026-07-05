<?php

namespace App\Controller\Admin;

use App\Entity\Permission;
use App\Repository\ContentTypeRepository;
use App\Repository\PermissionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Rompetomp\InertiaBundle\Architecture\InertiaInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/permissions')]
class PermissionController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    #[Route('', name: 'app_admin_permissions')]
    public function index(InertiaInterface $inertia, PermissionRepository $repo)
    {
        return $inertia->render('Permissions/Index', [
            'permissions' => $repo->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_permissions_new')]
    public function new(InertiaInterface $inertia, ContentTypeRepository $ctRepo)
    {
        return $inertia->render('Permissions/Form', [
            'permission' => null,
            'contentTypes' => $ctRepo->findAll(),
        ]);
    }

    #[Route('/save', name: 'app_admin_permissions_save', methods: ['POST'])]
    public function save(Request $request, ContentTypeRepository $ctRepo)
    {
        $data = $request->request->all();

        $permission = $data['id'] ? $this->em->getRepository(Permission::class)->find($data['id']) : new Permission();
        $permission->setName($data['name']);
        $permission->setCodename($data['codename']);

        if ($data['contentTypeId']) {
            $ct = $ctRepo->find($data['contentTypeId']);
            $permission->setContentType($ct);
        }

        $this->em->persist($permission);
        $this->em->flush();

        return $this->redirectToRoute('app_admin_permissions');
    }

    #[Route('/{id}/edit', name: 'app_admin_permissions_edit')]
    public function edit(InertiaInterface $inertia, Permission $permission, ContentTypeRepository $ctRepo)
    {
        return $inertia->render('Permissions/Form', [
            'permission' => $permission,
            'contentTypes' => $ctRepo->findAll(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_admin_permissions_delete', methods: ['POST'])]
    public function delete(Permission $permission)
    {
        $this->em->remove($permission);
        $this->em->flush();
        return $this->redirectToRoute('app_admin_permissions');
    }
}

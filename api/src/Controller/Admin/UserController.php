<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\UserGroup;
use App\Repository\GroupRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Rompetomp\InertiaBundle\Architecture\InertiaInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/users')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $hasher,
    ) {
    }

    #[Route('', name: 'app_admin_users')]
    public function index(InertiaInterface $inertia, UserRepository $repo)
    {
        return $inertia->render('Users/Index', [
            'users' => $repo->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_users_new')]
    public function new(InertiaInterface $inertia, GroupRepository $groupRepo)
    {
        return $inertia->render('Users/Form', [
            'user' => null,
            'groups' => $groupRepo->findAll(),
            'userGroupIds' => [],
        ]);
    }

    #[Route('/save', name: 'app_admin_users_save', methods: ['POST'])]
    public function save(Request $request, GroupRepository $groupRepo)
    {
        $data = $request->request->all();

        $user = $data['id'] ? $this->em->getRepository(User::class)->find($data['id']) : new User();
        $user->setName($data['name']);
        $user->setEmail($data['email']);

        if ($data['password']) {
            $user->setPassword($this->hasher->hashPassword($user, $data['password']));
        }

        foreach ($user->getUserGroups()->toArray() as $ug) {
            $this->em->remove($ug);
        }
        $user->getUserGroups()->clear();

        $selectedGroupIds = $data['groupIds'] ?? [];
        foreach ($selectedGroupIds as $groupId) {
            $group = $groupRepo->find($groupId);
            if ($group) {
                $ug = new UserGroup();
                $ug->setUser($user);
                $ug->setGroup($group);
                $this->em->persist($ug);
                $user->addUserGroup($ug);
            }
        }

        $this->em->persist($user);
        $this->em->flush();

        return $this->redirectToRoute('app_admin_users');
    }

    #[Route('/{id}/edit', name: 'app_admin_users_edit')]
    public function edit(InertiaInterface $inertia, User $user, GroupRepository $groupRepo)
    {
        return $inertia->render('Users/Form', [
            'user' => $user,
            'groups' => $groupRepo->findAll(),
            'userGroupIds' => array_map(
                fn ($ug) => $ug->getGroup()->getId(),
                $user->getUserGroups()->toArray()
            ),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_admin_users_delete', methods: ['POST'])]
    public function delete(User $user)
    {
        $this->em->remove($user);
        $this->em->flush();
        return $this->redirectToRoute('app_admin_users');
    }
}
